<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }</script>
</head>

<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Pedidos</h1>
            <div class="flex gap-3">
                <form action="{{ route('orders.index') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Buscar pedido..." value="{{ request('search') }}"
                        class="bg-slate-800 border border-slate-700 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:border-primary w-64">
                    <i class="fa-solid fa-search absolute left-3 top-3 text-slate-500"></i>
                </form>
                <a href="{{ route('products.sync') }}"
                    class="bg-primary hover:bg-cyan-500 text-slate-900 px-4 py-2 rounded-lg font-bold transition flex items-center gap-2 shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-rotate"></i> Atualizar
                </a>
            </div>
        </div>

        <div class="bg-card border border-slate-700 rounded-2xl shadow-xl overflow-hidden flex-1 flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-slate-800/50 text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-700">
                        <tr>
                            <th class="p-4 text-center">Canal</th>
                            <th class="p-4">Pedido / Data</th>
                            <th class="p-4">Cliente</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Valor</th>
                            <th class="p-4 text-right">Margem</th>
                            <th class="p-4 text-right">Lucro</th>
                            <th class="p-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-sm">
                        @forelse ($orders as $order)
                        <tr class="hover:bg-slate-800/50 transition cursor-pointer group"
                            onclick="window.location='{{ route('orders.show', $order->id) }}'">
                            <td class="p-4 text-center">
                                <div
                                    class="w-8 h-8 rounded bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto text-lg {{ $order->channel_icon['color'] }}">
                                    <i class="fa-brands {{ $order->channel_icon['icon'] }}"></i>
                                </div>
                            </td>
                            <td class="p-4">
                                <span
                                    class="font-mono text-xs text-slate-300 font-bold bg-slate-900 px-2 py-1 rounded block w-fit mb-1">#{{
                                    $order->external_id }}</span>
                                <span class="text-xs text-slate-500">{{ $order->date_created->format('d/m/Y H:i')
                                    }}</span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-white">{{ $order->safe_billing_name }}</div>
                                <div class="text-xs text-slate-500">{{ $order->safe_doc_number }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-[10px] border {{ $order->status_color }} font-bold">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-white">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="p-4 text-right">
                                <div class="text-xs text-blue-400 font-bold">R$ {{
                                    number_format($order->contribution_margin, 2, ',', '.') }}</div>
                            </td>
                            <td class="p-4 text-right">
                                <div class="text-emerald-400 font-bold text-base">R$ {{
                                    number_format($order->net_profit, 2, ',', '.') }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('orders.show', $order->id) }}"
                                    class="text-blue-400 hover:text-white transition"><i
                                        class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-slate-500">Nenhum pedido encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-800 bg-slate-900/30">{{ $orders->links() }}</div>
        </div>
    </main>
</body>

</html>