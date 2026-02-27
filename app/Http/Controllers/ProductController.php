<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Listagem com Lógica Master SKU (Multicontas)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->company_id) {
            return redirect()->route('dashboard');
        }

        // --- QUERY MASTER SKU ---
        // Agrupa pelo SKU. Se não tiver SKU, agrupa pelo ID externo.
        // Soma o estoque (SUM) e conta quantos anúncios (COUNT).
        $products = Product::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->select(
                DB::raw('COALESCE(NULLIF(sku, ""), external_id) as master_sku'), // Usa SKU ou External ID
                DB::raw('MAX(title) as title'), // Pega um título de exemplo
                DB::raw('MAX(image_url) as image_url'), // Pega uma imagem
                DB::raw('SUM(stock_quantity) as total_stock'), // SOMA o estoque de todas as contas
                DB::raw('COUNT(id) as listings_count'), // Conta anúncios vinculados
                DB::raw('AVG(sale_price) as avg_price') // Preço médio
            )
            ->groupBy('master_sku')
            ->orderBy('total_stock', 'desc')
            ->paginate(20);

        return view('products.index', compact('products'));
    }

    public function sync()
    {
        // Lógica de sync virá depois
        return back()->with('success', 'Sincronização agendada.');
    }
    
    public function customers()
    {
         // Placeholder para evitar erro de rota
         return view('dashboard', [
            'salesToday' => 0, 
            'ordersCountToday' => 0, 
            'salesMonth' => 0
        ]);
    }
}