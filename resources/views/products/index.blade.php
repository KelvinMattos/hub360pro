@extends('layouts.app')

@section('content')
<div class="p-6 bg-dark min-h-screen text-slate-300">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-boxes-stacked text-primary mr-2"></i>Catálogo
            Master SKU</h1>
    </div>

    <div class="bg-card border border-slate-700 rounded-xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-800 text-slate-400 font-bold uppercase text-xs">
                <tr>
                    <th class="p-4">SKU</th>
                    <th class="p-4">Produto</th>
                    <th class="p-4 text-center">Anúncios</th>
                    <th class="p-4 text-center">Preço Médio</th>
                    <th class="p-4 text-center">Estoque Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse ($products as $product)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="p-4 font-mono text-primary font-bold">{{ $product->sku }}</td>
                    <td class="p-4 flex items-center gap-3">
                        <img src="{{ $product->image_url ?? 'https://placehold.co/50' }}"
                            class="w-10 h-10 rounded bg-white object-contain">
                        <span class="text-white font-medium">{{ $product->title }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-slate-700 px-2 py-1 rounded text-xs">{{ $product->listings_count }} links</span>
                    </td>
                    <td class="p-4 text-center font-mono">R$ {{ number_format($product->avg_price, 2, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="font-bold text-lg text-emerald-400">{{ $product->total_stock }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Nenhum produto encontrado. Sincronize com o
                        Mercado Livre.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection