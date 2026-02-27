<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Integration;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SystemLog;
use App\Services\Adapters\MercadoLivreAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncOrdersCommand extends Command
{
    protected $signature = 'orders:sync {--full : Ignorado}';
    protected $description = 'Sincroniza pedidos e alimenta CRM';

    public function handle()
    {
        $integrations = Integration::whereNotNull('access_token')->where('platform', 'mercadolibre')->get();

        foreach ($integrations as $integration) {
            $this->info("Processando {$integration->company_id}...");
            $adapter = new MercadoLivreAdapter();
            
            try {
                // 1. RECENTES
                $recentOrders = $adapter->fetchOrders($integration, 50); 
                $this->saveOrders($integration, $recentOrders);

                // 2. HISTÓRICO
                $oldestOrder = Order::where('company_id', $integration->company_id)->orderBy('date_created', 'asc')->first();
                $olderThanDate = $oldestOrder ? $oldestOrder->date_created : now();
                
                $this->info("Buscando histórico anterior a: " . $olderThanDate->format('d/m/Y H:i'));
                $historyOrders = $adapter->fetchHistoricalBatch($integration, $olderThanDate);
                
                if (!empty($historyOrders)) {
                    $this->saveOrders($integration, $historyOrders);
                    $this->info("Importados " . count($historyOrders) . " pedidos antigos.");
                } else {
                    $this->info("Histórico em dia.");
                }

            } catch (\Throwable $e) {
                Log::error("Erro Cron ML: " . $e->getMessage());
                $this->error($e->getMessage());
            }
        }
    }

    private function saveOrders($integration, $orders)
    {
        foreach ($orders as $orderData) {
            // CRM LOGIC
            $customerId = null;
            $rawPhone = $orderData['billing_info_json']['buyer']['phone']['number'] ?? ($orderData['billing_info_json']['phone']['number'] ?? null);
            $rawEmail = $orderData['billing_info_json']['buyer']['email'] ?? null;
            $docClean = $orderData['billing_doc_number'] ? preg_replace('/[^0-9]/', '', $orderData['billing_doc_number']) : null;

            $customer = null;
            if ($docClean) {
                $customer = Customer::where('company_id', $integration->company_id)->where('doc_number', $docClean)->first();
            }
            if (!$customer && isset($orderData['billing_info_json']['buyer']['id'])) {
                $customer = Customer::where('company_id', $integration->company_id)
                    ->where('external_id', $orderData['billing_info_json']['buyer']['id'])
                    ->where('origin_channel', $integration->platform)->first();
            }

            $customerData = [
                'name' => $orderData['billing_name'] ?? $orderData['customer_name'],
                'doc_type' => $orderData['billing_doc_type'],
                'email' => $rawEmail, 'phone' => $rawPhone,
                'city' => $orderData['billing_city'] ?? $orderData['shipping_city'],
                'state' => $orderData['billing_state'] ?? $orderData['shipping_state'],
                'last_purchase_date' => $orderData['date_created']
            ];

            if ($customer) {
                if (empty($customer->origin_channel)) $customerData['origin_channel'] = $integration->platform; // CORREÇÃO AQUI TAMBÉM
                $customer->update($customerData);
            } else {
                $customerData['company_id'] = $integration->company_id;
                $customerData['doc_number'] = $docClean;
                $customerData['external_id'] = $orderData['billing_info_json']['buyer']['id'] ?? null;
                $customerData['origin_channel'] = $integration->platform;
                $customer = Customer::create($customerData);
            }
            $customerId = $customer->id;

            // ORDER LOGIC
            $order = Order::updateOrCreate(
                ['company_id' => $integration->company_id, 'external_id' => $orderData['external_id']],
                [
                    'integration_id' => $integration->id,
                    'customer_id' => $customerId,
                    'pack_id' => $orderData['pack_id'] ?? null,
                    'shipping_id' => $orderData['shipping_id'] ?? null,
                    'selling_channel' => $orderData['selling_channel'] ?? 'marketplace',
                    'billing_info_json' => $orderData['billing_info_json'] ?? null,
                    'customer_name' => $orderData['customer_name'] ?? 'Cliente',
                    'customer_doc' => $docClean,
                    'billing_doc_type' => $orderData['billing_doc_type'] ?? null,
                    'billing_doc_number' => $docClean,
                    'billing_name' => $orderData['billing_name'] ?? null,
                    'billing_ie' => $orderData['billing_ie'] ?? null,
                    'taxpayer_type' => $orderData['taxpayer_type'] ?? null,
                    'billing_address_line' => $orderData['billing_address_line'] ?? null,
                    'billing_number' => $orderData['billing_number'] ?? null,
                    'billing_zip' => $orderData['billing_zip'] ?? null,
                    'shipping_address_line' => $orderData['shipping_address_line'] ?? null,
                    'shipping_number' => $orderData['shipping_number'] ?? null,
                    'shipping_neighborhood' => $orderData['shipping_neighborhood'] ?? null,
                    'shipping_city' => $orderData['shipping_city'] ?? null,
                    'shipping_state' => $orderData['shipping_state'] ?? null,
                    'shipping_zip' => $orderData['shipping_zip'] ?? null,
                    'shipping_country' => $orderData['shipping_country'] ?? 'BR',
                    'shipping_comment' => $orderData['shipping_comment'] ?? null,
                    'total_amount' => $orderData['total_amount'] ?? 0,
                    'status' => $orderData['status'],
                    'payment_status' => $orderData['payment_status'] ?? $orderData['status'],
                    'payment_method' => $orderData['payment_method'] ?? 'unknown',
                    'date_created' => $orderData['date_created'],
                    'cost_fee_commission' => $orderData['cost_fee_commission'] ?? 0,
                    'cost_fee_fixed' => $orderData['cost_fee_fixed'] ?? 0,
                    'cost_fee_shipping' => $orderData['cost_fee_shipping'] ?? 0,
                    'cost_fee_ads' => $orderData['cost_fee_ads'] ?? 0,
                    'cost_fee_taxes' => $orderData['cost_fee_taxes'] ?? 0,
                    'cost_tax_platform' => $orderData['platform_cost'] ?? 0,
                    'cost_shipping_seller' => $orderData['shipping_cost'] ?? 0
                ]
            );

            $cmv = 0;
            foreach ($orderData['items'] as $itemData) {
                $prod = Product::where('company_id', $integration->company_id)->where('sku', $itemData['sku'])->first();
                $uCost = ($prod && $prod->cost_price > 0) ? $prod->cost_price : ($itemData['unit_price'] * 0.5);
                $cmv += ($uCost * $itemData['quantity']);
                OrderItem::updateOrCreate(
                    ['order_id' => $order->id, 'external_id' => $itemData['external_id']],
                    ['sku' => $itemData['sku'], 'title' => $itemData['title'], 'quantity' => $itemData['quantity'], 'unit_price' => $itemData['unit_price'], 'unit_cost' => $uCost]
                );
            }

            $imposto = $order->total_amount * (($integration->company->default_tax ?? 6) / 100);
            $lucro = $order->total_amount - $cmv - $orderData['platform_cost'] - $imposto;
            $order->update(['cost_products' => $cmv, 'cost_tax_fiscal' => $imposto, 'net_profit' => $lucro]);
            
            if ($customerId) {
                $c = Customer::find($customerId);
                $stats = $c->orders()->selectRaw('count(*) as c, sum(total_amount) as t')->first();
                $c->update(['orders_count' => $stats->c, 'total_spent' => $stats->t]);
            }
        }
    }
}