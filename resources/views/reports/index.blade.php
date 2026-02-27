<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intelligence Center | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script>tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }</script>
    <style>
        .glass { background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(12px); }
        [x-cloak] { display: none !important; }
        
        /* Estilos de Impressão (PDF) */
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { background: white !important; color: black !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .bg-card, .glass { background: white !important; border: 1px solid #ddd !important; box-shadow: none !important; }
            .text-white { color: #000 !important; }
            .text-slate-400, .text-slate-500 { color: #555 !important; }
            canvas { max-height: 300px !important; }
            h1, h2, h3 { color: #000 !important; }
        }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" x-data="reportsApp()">

    <div class="no-print h-full flex-shrink-0">
        @include('partials.sidebar')
    </div>

    <main class="flex-1 flex flex-col overflow-y-auto p-4 md:p-8 relative">
        
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="p-2 bg-primary/20 rounded-lg text-primary"><i class="fa-solid fa-chart-pie text-xl"></i></div>
                    <h1 class="text-3xl font-bold text-white">Intelligence Center</h1>
                </div>
                <p class="text-slate-400 text-sm">Visão estratégica: <strong class="text-white">{{ $start->format('d/m/Y') }}</strong> até <strong class="text-white">{{ $end->format('d/m/Y') }}</strong> ({{ $label }})</p>
            </div>

            <div class="flex flex-wrap gap-2 no-print">
                <form method="GET" action="{{ route('reports.index') }}" class="flex bg-card border border-slate-700 rounded-lg p-1">
                    @foreach(['today'=>'Hoje', 'yesterday'=>'Ontem', 'last_7_days'=>'7 Dias', 'this_month'=>'Este Mês', 'last_month'=>'Mês Passado'] as $k => $v)
                        <button type="submit" name="range" value="{{ $k }}" 
                                class="px-3 py-1.5 text-xs font-bold rounded transition {{ $range == $k ? 'bg-primary text-slate-900 shadow' : 'text-slate-400 hover:text-white' }}">
                            {{ $v }}
                        </button>
                    @endforeach
                </form>

                <div class="flex gap-2">
                    <button @click="printReport()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-print"></i> PDF
                    </button>
                    <button @click="downloadExcel()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-900/20">
                        <i class="fa-solid fa-file-excel"></i> Excel
                        <i class="fa-solid fa-spinner fa-spin ml-1" x-show="exporting"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-card border border-slate-700 p-6 rounded-2xl glass hover:border-primary/50 transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Faturamento</p>
                        <h3 class="text-3xl font-bold text-white mt-1">R$ {{ number_format($currentStats->revenue, 2, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 transition"><i class="fa-solid fa-sack-dollar text-xl"></i></div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="{{ $growth >= 0 ? 'text-emerald-400 bg-emerald-500/10' : 'text-red-400 bg-red-500/10' }} px-2 py-1 rounded font-bold flex items-center gap-1">
                        <i class="fa-solid {{ $growth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i> {{ number_format(abs($growth), 1) }}%
                    </span>
                    <span class="text-slate-500 ml-2">vs. período anterior</span>
                </div>
            </div>

            <div class="bg-card border border-slate-700 p-6 rounded-2xl glass hover:border-emerald-500/50 transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Lucro Líquido (DRE)</p>
                        <h3 class="text-3xl font-bold text-emerald-400 mt-1">R$ {{ number_format($currentStats->profit, 2, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition"><i class="fa-solid fa-wallet text-xl"></i></div>
                </div>
                <div class="mt-4 text-xs text-slate-500">Resultado final após custos e impostos.</div>
            </div>

            <div class="bg-card border border-slate-700 p-6 rounded-2xl glass hover:border-purple-500/50 transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Ticket Médio</p>
                        <h3 class="text-3xl font-bold text-white mt-1">R$ {{ number_format($currentStats->ticket, 2, ',', '.') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 group-hover:scale-110 transition"><i class="fa-solid fa-receipt text-xl"></i></div>
                </div>
                <div class="mt-4 text-xs text-slate-500">Média de valor por pedido aprovado.</div>
            </div>

            <div class="bg-card border border-slate-700 p-6 rounded-2xl glass hover:border-orange-500/50 transition duration-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total de Pedidos</p>
                        <h3 class="text-3xl font-bold text-white mt-1">{{ $currentStats->total_orders }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center text-orange-400 group-hover:scale-110 transition"><i class="fa-solid fa-boxes-packing text-xl"></i></div>
                </div>
                <div class="mt-4 text-xs text-slate-500">Volume total de vendas processadas.</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 bg-card border border-slate-700 rounded-2xl p-6 glass shadow-lg">
                <h3 class="font-bold text-white mb-6 text-sm uppercase flex items-center gap-2"><i class="fa-solid fa-chart-area text-primary"></i> Curva de Evolução</h3>
                <div class="relative w-full h-80">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            <div class="bg-card border border-slate-700 rounded-2xl p-6 glass shadow-lg flex flex-col">
                <h3 class="font-bold text-white mb-6 text-sm uppercase flex items-center gap-2"><i class="fa-solid fa-share-nodes text-yellow-400"></i> Share por Canal</h3>
                <div class="relative w-full h-64 flex-1 flex items-center justify-center">
                    <canvas id="channelChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($channelStats as $channel)
                    <div class="flex justify-between text-xs border-b border-slate-800 pb-1">
                        <span class="text-white capitalize">{{ $channel->platform }}</span>
                        <span class="text-slate-400 font-mono">R$ {{ number_format($channel->total, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <div class="bg-card border border-slate-700 rounded-2xl p-6 glass">
                <h3 class="font-bold text-white mb-6 text-sm uppercase flex items-center gap-2"><i class="fa-solid fa-filter text-blue-400"></i> Funil Operacional</h3>
                <div class="space-y-4">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-emerald-400 bg-emerald-500/10">
                                Pagos / Aprovados
                            </span>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-emerald-400">{{ $funnel['paid'] }}</span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-slate-800">
                            <div style="width: 100%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-emerald-500"></div>
                        </div>
                    </div>
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-400 bg-blue-500/10">
                                Em Separação / Envio
                            </span>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-blue-400">{{ $funnel['shipping'] }}</span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-slate-800">
                            <div style="width: {{ $funnel['paid'] > 0 ? ($funnel['shipping'] / $funnel['paid']) * 100 : 0 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
                        </div>
                    </div>
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-slate-400 bg-slate-700/50">
                                Entregues (Finalizados)
                            </span>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-slate-400">{{ $funnel['delivered'] }}</span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-slate-800">
                            <div style="width: {{ $funnel['paid'] > 0 ? ($funnel['delivered'] / $funnel['paid']) * 100 : 0 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-slate-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-card border border-slate-700 rounded-2xl p-6 glass">
                <h3 class="font-bold text-white mb-4 text-sm uppercase flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation text-red-500"></i> Ruptura de Estoque (Top 5)</h3>
                @if($noStock->isEmpty())
                    <div class="h-full flex items-center justify-center text-slate-500 text-sm">Nenhum produto zerado.</div>
                @else
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-slate-800">
                            @foreach($noStock as $p)
                            <tr>
                                <td class="py-3 text-white font-medium">{{ Str::limit($p->title, 40) }}</td>
                                <td class="py-3 text-right"><span class="bg-red-500/10 text-red-500 px-2 py-1 rounded font-bold">0 un</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="bg-card border border-slate-700 rounded-2xl p-6 glass shadow-xl">
            <h3 class="font-bold text-white mb-6 text-sm uppercase flex items-center gap-2"><i class="fa-solid fa-trophy text-yellow-400"></i> Ranking de Produtos (Curva A)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-700 text-xs uppercase">
                            <th class="p-3 w-10">#</th>
                            <th class="p-3">Produto</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3 text-right">Qtd. Vendida</th>
                            <th class="p-3 text-right">Total (R$)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($topProducts as $idx => $prod)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="p-3 font-bold text-slate-500">{{ $idx + 1 }}</td>
                            <td class="p-3 font-medium text-white">{{ $prod->title }}</td>
                            <td class="p-3 text-slate-500 font-mono text-xs">{{ $prod->sku }}</td>
                            <td class="p-3 text-right text-white">{{ $prod->qty }}</td>
                            <td class="p-3 text-right text-emerald-400 font-bold">R$ {{ number_format($prod->total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        function reportsApp() {
            return {
                exporting: false,
                init() {
                    this.initCharts();
                },
                initCharts() {
                    // Gráfico de Evolução
                    new Chart(document.getElementById('evolutionChart'), {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: 'Vendas (R$)',
                                data: @json($chartValues),
                                borderColor: '#06B6D4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
                                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                            }
                        }
                    });

                    // Gráfico de Canais
                    const channelData = @json($channelStats);
                    new Chart(document.getElementById('channelChart'), {
                        type: 'doughnut',
                        data: {
                            labels: channelData.map(c => c.platform),
                            datasets: [{
                                data: channelData.map(c => c.total),
                                backgroundColor: ['#FACC15', '#FB923C', '#E11D48', '#10B981'], // Cores para ML, Shopee, TikTok, Bling
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'right', labels: { color: '#fff' } } }
                        }
                    });
                },
                printReport() {
                    window.print();
                },
                async downloadExcel() {
                    this.exporting = true;
                    try {
                        // Busca dados brutos da API interna
                        const response = await fetch("{{ route('reports.export') }}?type=sales");
                        const data = await response.json();
                        
                        // Cria planilha
                        const ws = XLSX.utils.json_to_sheet(data);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, "Vendas");
                        
                        // Download
                        XLSX.writeFile(wb, "Relatorio_Vendas_PrismaHUB.xlsx");
                    } catch (e) {
                        alert('Erro ao gerar Excel. Tente novamente.');
                    } finally {
                        this.exporting = false;
                    }
                }
            }
        }
    </script>
</body>
</html>