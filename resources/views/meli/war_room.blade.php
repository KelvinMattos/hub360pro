<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>War Room | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script> 
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#3483FA', meli: '#FFE600' } } } }
    </script>
    <style> 
        [x-cloak] { display: none; }
        .gauge-container { width: 180px; height: 90px; position: relative; overflow: hidden; margin: 0 auto; }
        .gauge-bg { position: absolute; width: 180px; height: 180px; border: 20px solid #334155; border-radius: 50%; }
        .gauge-fill { position: absolute; width: 180px; height: 180px; border: 20px solid transparent; border-top-color: #F59E0B; border-radius: 50%; transform-origin: 50% 50%; transition: transform 1s ease-out; transform: rotate(0deg); }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" x-data="warRoom()">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-crosshairs text-meli"></i> War Room
            </h1>
            <p class="text-slate-400 text-sm">Inteligência de mercado e espionagem.</p>
        </div>

        <div class="bg-card border border-slate-700 rounded-xl p-6 shadow-lg mb-6">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Termo de Busca</label>
                    <input type="text" x-model="query" @keydown.enter="search()" placeholder="Ex: Tênis Nike Revolution 6" 
                           class="w-full bg-dark border border-slate-600 rounded-lg px-4 py-3 text-white focus:border-meli outline-none">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Meu Preço (R$)</label>
                    <input type="number" x-model="myPrice" class="w-full bg-dark border border-slate-600 rounded-lg px-4 py-3 text-white font-bold focus:border-meli outline-none">
                </div>
                <button @click="search()" class="bg-meli hover:bg-yellow-400 text-black font-bold px-6 py-3 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-radar" :class="{'fa-spin': loading}"></i> Espionar
                </button>
            </div>
        </div>

        <div x-show="results" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-card border border-slate-700 rounded-xl overflow-hidden shadow-lg">
                <div class="p-4 bg-slate-800/50 border-b border-slate-700 flex justify-between">
                    <h3 class="font-bold text-white">Top 10 Concorrentes</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-800 text-slate-400 text-xs uppercase">
                        <tr><th class="p-3">Anúncio</th><th class="p-3 text-right">Preço</th><th class="p-3 text-center">Reputação</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <template x-for="item in results.competitors" :key="item.id">
                            <tr class="hover:bg-slate-700/30 transition" :class="{'bg-meli/10': item.price < myPrice}">
                                <td class="p-3 flex gap-3 items-center">
                                    <img :src="item.thumbnail" class="w-10 h-10 rounded border border-slate-600 bg-white object-contain">
                                    <a :href="item.permalink" target="_blank" class="text-white hover:text-primary line-clamp-1 max-w-xs" x-text="item.title"></a>
                                </td>
                                <td class="p-3 text-right font-bold text-white">R$ <span x-text="item.price.toFixed(2)"></span></td>
                                <td class="p-3 text-center"><span class="text-[10px] px-2 py-0.5 rounded border border-slate-600 bg-slate-800" x-text="item.reputation"></span></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="bg-card border border-slate-700 rounded-xl p-6 shadow-lg flex flex-col items-center justify-center">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-6">Competitividade</h3>
                <div class="gauge-container">
                    <div class="gauge-bg"></div>
                    <div class="gauge-fill" :style="getGaugeStyle()"></div>
                </div>
                <div class="text-center mt-4">
                    <p class="text-3xl font-bold text-white" x-text="(results.stats.gap > 0 ? '+' : '') + results.stats.gap + '%'"></p>
                    <p class="text-sm" :class="getColorClass()" x-text="getStatusText()"></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        function warRoom() {
            return {
                query: '', myPrice: 0, loading: false, results: null,
                search() {
                    if(!this.query) return alert('Digite algo!');
                    this.loading = true;
                    fetch(`{{ route('meli.war_room.search') }}?q=${encodeURIComponent(this.query)}&my_price=${this.myPrice}`)
                        .then(res => res.json())
                        .then(data => { this.results = data; this.loading = false; })
                        .catch(() => { alert('Erro na busca.'); this.loading = false; });
                },
                getGaugeStyle() {
                    if(!this.results) return '';
                    let gap = this.results.stats.gap;
                    let rot = 0; // 0 (Esq/Vermelho) -> 90 (Meio/Amarelo) -> 180 (Dir/Verde)
                    let color = '#EF4444';
                    
                    if (gap <= 0) { rot = 180; color = '#10B981'; } // Barato
                    else if (gap <= 10) { rot = 90; color = '#F59E0B'; } // Médio
                    else { rot = 0; color = '#EF4444'; } // Caro

                    return `transform: rotate(${rot}deg); border-top-color: ${color}`;
                },
                getColorClass() {
                    const s = this.results.stats.status;
                    return s === 'winning' ? 'text-emerald-400' : (s === 'fighting' ? 'text-yellow-400' : 'text-red-400');
                },
                getStatusText() {
                    const s = this.results.stats.status;
                    if(s === 'winning') return 'Preço Vencedor!';
                    if(s === 'fighting') return 'Competitivo';
                    return 'Preço Alto';
                }
            }
        }
    </script>
</body>
</html>