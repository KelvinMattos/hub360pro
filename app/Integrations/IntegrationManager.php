<?php

namespace App\Integrations;

use App\Models\Integration;
use Exception;

class IntegrationManager
{
    /**
     * Resolve a implementação correta para uma integração
     */
    public function make(Integration $integration): AbstractMarketplace
    {
        return match ($integration->platform) {
                'mercadolibre' => new MercadoLivreIntegration($integration),
                // 'shopee' => new ShopeeIntegration($integration),
                default => throw new Exception("Plataforma [{$integration->platform}] não suportada."),
            };
    }
}