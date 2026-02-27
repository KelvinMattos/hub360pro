<?php

namespace App\Contracts;

use App\Models\Integration;

interface MarketplaceAdapter
{
    public function checkConnection(Integration $integration): bool;
    
    public function fetchOrders(Integration $integration, $limit = 50);
    
    public function fetchProducts(Integration $integration);
    
    public function getShipmentLabel(Integration $integration, string $shippingId);
    
    // Assinatura Blindada
    public function updatePrice(Integration $integration, string $sku, float $price, ?float $promotionalPrice = null);
    
    public function updateProduct(Integration $integration, string $mlId, float $price, int $stock);
    
    public function fetchHistoricalBatch(Integration $integration, $olderThan);
}