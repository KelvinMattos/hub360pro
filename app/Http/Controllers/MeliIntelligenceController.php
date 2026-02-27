<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MeliIntelligenceController extends Controller
{
    public function calculator() {
        return view('meli.calculator'); 
    }

    public function warRoom() {
        return view('meli.war_room');
    }

    public function searchCompetitors(Request $request)
    {
        $term = $request->input('q');
        $myPrice = (float) $request->input('my_price', 0);
        
        if (!$term) return response()->json(['error' => 'Termo inválido'], 400);

        // Busca na API Pública (Modo Espião)
        $response = Http::get("https://api.mercadolibre.com/sites/MLB/search", [
            'q' => $term,
            'limit' => 10,
            'sort' => 'relevance' // Prioriza quem vende mais
        ]);

        if ($response->failed()) return response()->json(['error' => 'Erro API'], 500);

        $results = $response->json()['results'];
        $competitors = [];
        $marketPrices = [];

        foreach ($results as $item) {
            $price = (float)$item['price'];
            $marketPrices[] = $price;

            $competitors[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'price' => $price,
                'permalink' => $item['permalink'],
                'thumbnail' => $item['thumbnail'],
                'sold_quantity' => $item['sold_quantity'] ?? 0,
                'reputation' => $item['seller']['seller_reputation']['level_id'] ?? 'N/D',
                'free_shipping' => $item['shipping']['free_shipping'] ?? false
            ];
        }

        $minPrice = !empty($marketPrices) ? min($marketPrices) : 0;
        
        // Cálculo de GAP
        $gap = 0;
        $status = 'neutral';
        
        if ($myPrice > 0 && $minPrice > 0) {
            $gap = (($myPrice - $minPrice) / $minPrice) * 100;
            
            if ($gap <= 0) $status = 'winning';   // Líder
            elseif ($gap <= 5) $status = 'fighting'; // Competitivo
            else $status = 'losing';            // Caro
        }

        return response()->json([
            'competitors' => $competitors,
            'stats' => [
                'min_price' => $minPrice,
                'gap' => round($gap, 2),
                'status' => $status
            ]
        ]);
    }
}