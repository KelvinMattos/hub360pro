<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $customer->name }} | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }</script>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex flex-col">
    
    <div class="bg-card border-b border-slate-700 p-4 flex justify-between items-center shadow-lg z-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
            <div class="h-8 w-px bg-slate-700 mx-2"></div>
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    {{ $customer->name }}
                </h1>
                <p class="text-xs text-slate-400">Cliente desde {{ $customer->created_at->format('M/Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {!! $customer->channel_badge !!}
            {!! $customer->status_label !!}
        </div>
    </div>

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="space-y-6">
                <div class="bg-card border border-slate-700 rounded-xl p-6 shadow-lg">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-20 h-20 rounded-full bg-slate-800 flex items-center justify-center text-3xl text-slate-500 mb-3 border border-slate-700">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h2 class="text-lg font-bold text-white text-center">{{ $customer->name }}</h2>
                        <p class="text-sm text-slate-500 font-mono">{{ $customer->formatted_doc }}</p>
                    </div>
                    
                    <div class="space-y-4 text-sm border-t border-slate-700 pt-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-primary"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-500">Email</p>
                                <p class="text-white break-all">{{ $customer->email ?? 'Não disponível' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-primary"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-500">Telefone</p>
                                <p class="text-white">{{ $customer->phone ?? 'Não disponível' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-primary"><i class="fa-solid fa-map-location-dot"></i></div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-500">Localização</p>
                                <p class="text-white">{{ $customer->city ?? '--' }} - {{ $customer->state ?? '--' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-white uppercase mb-4">Métricas</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-800/50 p-3 rounded-lg border border-slate-700/50">
                            <p class="text-[10px] uppercase font-bold text-slate-500">Total Gasto</p>
                            <p class="text-xl font-bold text-emerald-400">R$ {{ number_format($customer->total_spent, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-800/50 p-3 rounded-lg border border-slate-700/50">
                            <p class="text-[10px] uppercase font-bold text-slate-500">Pedidos</p>
                            <p class="text-xl font-bold text-white">{{ $customer->orders_count }}</p>
                        </div>
                        <div class="col-span-2 bg-slate-800/50 p-3 rounded-lg border border-slate-700/50">
                            <p class="text-[10px] uppercase font-bold text-slate-500">Última Compra</p>
                            <p class="text-sm text-white">{{ $customer->last_purchase_date ? $customer->last_purchase_date->format('d/m/Y H:i') : '--' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-card border border-slate-700 rounded-xl overflow-hidden shadow-lg">
                    <div class="p-6 border-b border-slate-700">
                        <h3 class="text-lg font-bold text-white">Histórico de Compras</h3>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-800/50 text-slate-400 text-xs uppercase">
                            <tr>
                                <th class="p-4 text-center">Canal</th>
                                <th class="p-4">Data / ID</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Valor</th>
                                <th class="p-4 text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($customer->orders as $order)
                            <tr class="hover:bg-slate-700/50 cursor-pointer" onclick="window.location='{{ route('orders.show', $order->id) }}'">
                                <td class="p-4 text-center">
                                    <div class="w-8 h-8 rounded bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto text-lg {{ $order->channel_icon['color'] }}">
                                        <i class="fa-brands {{ $order->channel_icon['icon'] }}"></i>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="text-white font-mono font-bold">#{{ $order->external_id }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->date_created->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded text-[10px] border {{ $order->status_color }} font-bold">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="p-4 text-right font-bold text-white">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-blue-400 hover:text-white"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</body>
</html>