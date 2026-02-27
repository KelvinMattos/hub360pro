<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Agrupa pedidos por Documento (CPF/CNPJ) para criar a lista de clientes
        // Removido 'customer_email' do groupBy para evitar erro de coluna inexistente
        $query = Order::where('company_id', Auth::user()->company_id)
            ->whereNotNull('billing_doc_number')
            ->select(
                'billing_doc_number',
                'customer_name',
                DB::raw('MAX(date_created) as last_purchase'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->groupBy('billing_doc_number', 'customer_name');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                  ->orWhere('billing_doc_number', 'like', "%{$term}%");
            });
        }

        $customers = $query->orderBy('last_purchase', 'desc')->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function show($doc)
    {
        // Busca histórico completo do cliente pelo Documento
        $orders = Order::where('company_id', Auth::user()->company_id)
            ->where('billing_doc_number', $doc)
            ->orderBy('date_created', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('customers.index')->with('error', 'Cliente não encontrado.');
        }

        // Pega dados do registro mais recente
        $customer = $orders->first();

        // Estatísticas do Cliente
        $stats = [
            'total_spent' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'avg_ticket' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'first_purchase' => $orders->last()->date_created
        ];

        return view('customers.show', compact('customer', 'orders', 'stats'));
    }
}