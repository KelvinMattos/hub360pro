<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planejamento de Estoque Master | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script> 

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: '#0F172A', card: '#1E293B', primary: '#3483FA', 
                        success: '#00A650', warning: '#F59E0B', danger: '#EF4444', meli: '#FFE600'
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 flex flex-col min-w-0" 
          x-data="inventoryApp({{ json_encode($inventoryData) }})">

        <header class="bg-card border-b border-slate-700 p-6 z-20 shadow-md">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                
                <div class="flex-1 w-full xl:w-auto">
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-layer-group text-primary"></i> Estoque 360º (Master SKU)
                    </h1>
                    <div class="relative max-w-xl">
                        <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-500"></i>
                        <input type="text" x-model="search" 
                               placeholder="Pesquise por SKU, Título ou código MLB..." 
                               class="w-full bg-dark border border-slate-600 rounded-xl py-3 pl-12 pr-4 text-white focus:border-primary outline-none transition shadow-inner">
                    </div>
                </div>

                <div class="flex gap-4 w-full xl:w-auto overflow-x-auto pb-2 xl:pb-0">
                    @if($stats['lost_money'] > 0)
                    <div class="bg-red-500/10 border border-red-500/30 px-5 py-3 rounded-xl min-w-[160px] animate-pulse">
                        <span class="text-[10px] text-red-400 uppercase font-bold flex items-center gap-2">
                            <i class="fa-solid fa-money-bill-wave"></i> Perda Potencial
                        </span>
                        <span class="text-lg font-bold text-white block mt-1">R$ {{ number_format($stats['lost_money'], 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="bg-red-500/10 border border-red-500/30 px-5 py-3 rounded-xl min-w-[140px]">
                        <span class="text-[10px] text-red-400 uppercase font-bold flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Críticos
                        </span>
                        <span class="text-2xl font-bold text-white block mt-1">{{ $stats['critical_count'] }}</span>
                    </div>

                    <div class="bg-yellow-500/10 border border-yellow-500/30 px-5 py-3 rounded-xl min-w-[160px]">
                        <span class="text-[10px] text-yellow-400 uppercase font-bold flex items-center gap-2">
                            <i class="fa-solid fa-anchor"></i> Imobilizado
                        </span>
                        <span class="text-lg font-bold text-white block mt-1">R$ {{ number_format($stats['immobilized'], 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-emerald-500/10 border border-emerald-500/30 px-5 py-3 rounded-xl min-w-[180px]">
                        <span class="text-[10px] text-emerald-400 uppercase font-bold flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i> Reposição (45d)
                        </span>
                        <span class="text-2xl font-bold text-white block mt-1">R$ {{ number_format($stats['total_investment'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-6">
                <div class="flex bg-dark rounded-lg p-1 border border-slate-700">
                    <button @click="filterCurve = 'all'" :class="filterCurve === 'all' ? 'bg-slate-700 text-white shadow' : 'text-slate-500 hover:text-slate-300'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">Todos</button>
                    <button @click="filterCurve = 'A'" :class="filterCurve === 'A' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : 'text-slate-500 hover:text-yellow-500'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">A</button>
                    <button @click="filterCurve = 'B'" :class="filterCurve === 'B' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'text-slate-500 hover:text-blue-500'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">B</button>
                    <button @click="filterCurve = 'C'" :class="filterCurve === 'C' ? 'bg-slate-700 text-slate-300' : 'text-slate-500 hover:text-slate-300'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">C</button>
                </div>
                
                 <div class="flex bg-dark rounded-lg p-1 border border-slate-700">
                    <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-slate-700 text-white shadow' : 'text-slate-500 hover:text-slate-300'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">Todos Status</button>
                    <button @click="filterStatus = 'critical'" :class="filterStatus === 'critical' ? 'bg-red-500 text-white shadow' : 'text-slate-500 hover:text-red-400'" class="px-3 py-1.5 rounded-md text-xs font-bold transition">🚨 Críticos</button>
                </div>

                <span class="ml-auto text-xs text-slate-500 self-center">
                    <strong class="text-white" x-text="filteredItems.length"></strong> registros
                </span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 bg-dark custom-scrollbar">
            <div class="bg-card border border-slate-700 rounded-xl overflow-hidden shadow-xl min-w-[1000px]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-800 text-slate-400 text-xs uppercase font-bold tracking-wider sticky top-0 z-10 shadow-md">
                        <tr>
                            <th class="p-4 cursor-pointer hover:text-white" @click="sortBy('title')">Produto <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-center cursor-pointer hover:text-white" @click="sortBy('curve')">ABC <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-center cursor-pointer hover:text-white" @click="sortBy('velocity')">Giro (Dia) <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-center cursor-pointer hover:text-white" @click="sortBy('stock')">Estoque <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-center w-40 cursor-pointer hover:text-white" @click="sortBy('doc')">Cobertura <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-center text-primary border-b-2 border-primary cursor-pointer" @click="sortBy('suggestion')">Sugestão <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                            <th class="p-4 text-right cursor-pointer hover:text-white" @click="sortBy('investment_needed')">Custo <i class="fa-solid fa-sort ml-1 opacity-50"></i></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <template x-for="item in filteredItems" :key="item.sku">
                            <tr class="hover:bg-slate-700/40 transition group">
                                <td class="p-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-white rounded-lg border border-slate-600 flex-shrink-0 p-0.5 overflow-hidden">
                                            <img :src="item.image || 'https://placehold.co/100x100?text=No+Image'" class="w-full h-full object-contain">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-sm line-clamp-1" x-text="item.title"></div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-300 border border-slate-600 font-mono" x-text="item.sku"></span>
                                                <template x-if="item.cannibal">
                                                    <span class="text-[9px] text-red-400 bg-red-500/10 px-1.5 py-0.5 rounded border border-red-500/20"><i class="fa-solid fa-triangle-exclamation"></i> Canibalismo</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-black border text-sm"
                                          :class="{'bg-yellow-500/20 text-yellow-500 border-yellow-500/50': item.curve === 'A', 'bg-blue-500/20 text-blue-500 border-blue-500/50': item.curve === 'B', 'bg-slate-700 text-slate-500 border-slate-600': item.curve === 'C'}" 
                                          x-text="item.curve"></span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="font-bold text-white" x-text="formatDecimal(item.velocity)"></div>
                                    <div class="text-[10px] text-slate-500" x-text="item.frequency"></div>
                                </td>
                                <td class="p-4 text-center text-lg font-bold text-white" x-text="item.stock"></td>
                                <td class="p-4">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-bold" :class="{'text-red-400': item.doc <= 7, 'text-white': item.doc > 7}" x-text="item.doc > 365 ? '+365d' : item.doc + ' dias'"></span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full" 
                                             :class="{'bg-red-500': item.doc <= 7, 'bg-yellow-500': item.doc > 7 && item.doc <= 15, 'bg-emerald-500': item.doc > 15}" 
                                             :style="'width: ' + Math.min((item.doc / 45) * 100, 100) + '%'"></div>
                                    </div>
                                </td>
                                <td class="p-4 text-center bg-primary/5 border-l border-r border-slate-700/50">
                                    <template x-if="item.suggestion > 0">
                                        <div class="text-xl font-extrabold text-primary" x-text="'+' + item.suggestion"></div>
                                    </template>
                                    <template x-if="item.suggestion == 0"><i class="fa-solid fa-check text-emerald-500/50 text-xl"></i></template>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="font-mono text-slate-300 font-bold text-xs" x-text="item.investment_needed > 0 ? 'R$ ' + formatMoney(item.investment_needed) : '--'"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div x-show="filteredItems.length === 0" class="flex flex-col items-center justify-center py-20 text-slate-500">
                <i class="fa-solid fa-filter-circle-xmark text-4xl mb-4 opacity-50"></i>
                <p>Nenhum produto encontrado.</p>
            </div>
        </div>
    </main>

    <script>
        function inventoryApp(initialData) {
            return {
                items: initialData,
                search: '',
                filterCurve: 'all',
                filterStatus: 'all',
                sortCol: 'doc',
                sortAsc: true,

                get filteredItems() {
                    let result = this.items;
                    if (this.search) {
                        const s = this.search.toLowerCase();
                        result = result.filter(i => i.title.toLowerCase().includes(s) || i.sku.toLowerCase().includes(s));
                    }
                    if (this.filterCurve !== 'all') result = result.filter(i => i.curve === this.filterCurve);
                    if (this.filterStatus !== 'all') result = result.filter(i => i.status === this.filterStatus);

                    return result.sort((a, b) => {
                        let valA = a[this.sortCol], valB = b[this.sortCol];
                        if (typeof valA === 'string') { valA = valA.toLowerCase(); valB = valB.toLowerCase(); }
                        
                        if (valA < valB) return this.sortAsc ? -1 : 1;
                        if (valA > valB) return this.sortAsc ? 1 : -1;
                        return 0;
                    });
                },
                sortBy(col) {
                    if (this.sortCol === col) this.sortAsc = !this.sortAsc;
                    else { 
                        this.sortCol = col; 
                        this.sortAsc = ['velocity', 'stock', 'suggestion', 'investment_needed'].includes(col) ? false : true; 
                    }
                },
                formatMoney(v) { return Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }); },
                formatDecimal(v) { return Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 1 }); }
            }
        }
    </script>
</body>
</html>