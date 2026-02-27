<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Segurança para usuário novo
        if (!$user || !$user->company_id) {
            return view('dashboard', [
                'salesToday' => 0, 
                'ordersCountToday' => 0, 
                'salesMonth' => 0
            ]);
        }

        $hoje = now()->startOfDay();
        $mes = now()->startOfMonth();

        // Query Base Unificada
        $baseQuery = Order::where('company_id', $user->company_id)
            ->whereIn('status', ['paid', 'shipped', 'delivered', 'accredited']);

        // 1. Vendas Hoje (R$) - Usa total_paid_amount definido na migration
        $salesToday = (clone $baseQuery)
            ->where('created_at', '>=', $hoje)
            ->sum('total_paid_amount') ?? 0;

        // 2. Pedidos Hoje (Qtd)
        $ordersCountToday = (clone $baseQuery)
            ->where('created_at', '>=', $hoje)
            ->count();

        // 3. Faturamento Mês (R$)
        $salesMonth = (clone $baseQuery)
            ->where('created_at', '>=', $mes)
            ->sum('total_paid_amount') ?? 0;

        return view('dashboard', compact('salesToday', 'ordersCountToday', 'salesMonth'));
    }
}