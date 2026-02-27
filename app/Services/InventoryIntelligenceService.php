<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem; 
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryIntelligenceService
{
    public function getAggregatedInventoryStats(int $companyId, int $days = 30, int $leadTime = 15): Collection
    {
        // 1. Busca Direta e Otimizada (Evita Eloquent Relationships Complexos)
        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->addSelect([
                'products.*',
                // Subquery Vendas
                'sales_qty' => OrderItem::selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('created_at', '>=', now()->subDays($days)),
                // Subquery Receita
                'revenue_period' => OrderItem::selectRaw('COALESCE(SUM(unit_price * quantity), 0)')
                    ->whereColumn('product_id', 'products.id')
                    ->where('created_at', '>=', now()->subDays($days))
            ])
            ->get();

        // 2. Agrupamento por Master SKU
        $aggregated = $products->groupBy(function ($item) {
            return $item->sku ?: $item->external_id; 
        })->map(function ($group, $masterSku) use ($days, $leadTime) {
            
            $masterProduct = $group->first();
            $totalStock = $group->sum('stock_quantity');
            $totalSales = $group->sum('sales_qty');
            $totalRevenue = $group->sum('revenue_period');
            $avgCost = $group->avg('cost_price') ?? 0;

            $velocity = $days > 0 ? ($totalSales / $days) : 0;
            $doc = $velocity > 0 ? round($totalStock / $velocity) : 999;
            
            $frequencyText = "Sem vendas";
            if ($velocity > 0) {
                $freq = 1 / $velocity;
                $frequencyText = $freq < 1 ? number_format($velocity, 1) . "/dia" : "1 a cada " . round($freq) . "d";
            }

            $targetStock = $velocity * (45 + $leadTime);
            $suggestion = max(0, ceil($targetStock - $totalStock));

            $status = 'healthy';
            if ($totalSales == 0) $status = 'stagnant';
            elseif ($doc <= 7) $status = 'critical';
            elseif ($doc <= 15) $status = 'alert';
            elseif ($doc > 120) $status = 'overstock';

            $cannibalAlert = false;
            if ($group->count() > 1) {
                $prices = $group->pluck('sale_price');
                if ($prices->max() > 0 && ($prices->max() - $prices->min()) < ($prices->max() * 0.05)) $cannibalAlert = true;
            }

            return [
                'id' => $masterProduct->id,
                'sku' => $masterSku,
                'title' => $masterProduct->title,
                'image' => $masterProduct->json_data['thumbnail'] ?? $masterProduct->image_url ?? '',
                'mlbs' => $group->pluck('external_id')->values()->toArray(),
                'accounts_count' => $group->pluck('company_id')->unique()->count(),
                'stock' => $totalStock,
                'sales_30d' => $totalSales,
                'revenue_30d' => $totalRevenue,
                'velocity' => $velocity,
                'frequency' => $frequencyText,
                'doc' => $doc,
                'suggestion' => $suggestion,
                'investment_needed' => $suggestion * $avgCost,
                'immobilized_value' => ($doc > 90) ? ($totalStock * $avgCost) : 0,
                'lost_revenue' => ($totalStock == 0 && $velocity > 0) ? ($velocity * ($masterProduct->sale_price ?? 0) * 7) : 0,
                'status' => $status,
                'cannibal' => $cannibalAlert,
                'group_size' => $group->count(),
                'curve' => 'C' 
            ];
        });

        // 3. Curva ABC
        $sorted = $aggregated->sortByDesc('revenue_30d');
        $totalRevenue = $sorted->sum('revenue_30d');
        $accumulated = 0;

        return $sorted->map(function ($item) use (&$accumulated, $totalRevenue) {
            $accumulated += $item['revenue_30d'];
            $percentage = $totalRevenue > 0 ? ($accumulated / $totalRevenue) * 100 : 0;
            
            if ($percentage <= 80) $item['curve'] = 'A';
            elseif ($percentage <= 95) $item['curve'] = 'B';
            else $item['curve'] = 'C';

            return (object) $item;
        })->values();
    }
}