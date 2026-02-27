<?php

namespace App\Services\Adapters;

use App\Contracts\MarketplaceAdapter;
use App\Models\Integration;

class ShopeeAdapter implements MarketplaceAdapter
{
    public function checkConnection(Integration $integration): bool
    {
        return !empty($integration->client_id);
    }

    public function fetchProducts(Integration $integration)
    {
        $products = [];
        $qtd = rand(3, 8);

        for ($i = 0; $i < $qtd; $i++) {
            $price = rand(20, 150) + 0.50;
            $products[] = [
                'external_id' => 'SHOPEE-' . rand(100000, 999999),
                'title' => '[FRETE GRÁTIS] ' . $this->getRandomTitle(),
                'sku' => 'SH-' . strtoupper(substr(md5(rand()), 0, 6)),
                'price' => $price,
                'promotional_price' => null,
                'stock' => rand(50, 500),
                'status' => 'live',
                'image_url' => null
            ];
        }

        return $products;
    }

    public function fetchOrders(Integration $integration)
    {
        return [];
    }
    public function updatePrice(Integration $integration, $sku, $price, $promoPrice = null)
    {
        return true;
    }

    public function getShipmentLabel(Integration $integration, string $shippingId)
    {
        return null;
    }

    public function updateProduct(Integration $integration, string $externalId, float $price, int $stock)
    {
        return true;
    }

    public function fetchHistoricalBatch(Integration $integration, $olderThan)
    {
        return [];
    }

    private function getRandomTitle()
    {
        $nouns = ['Capinha Celular', 'Película Vidro', 'Cabo USB-C', 'Meia Cano Alto', 'Organizador de Cabos'];
        return $nouns[array_rand($nouns)];
    }
}