<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Dashboard 360 | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>tailwind.config = { darkMode: 'class', theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B' } } } }</script>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" x-data="dashboard()">
    @include('components.sidebar')
    <main class="flex-1 overflow-y-auto p-8 relative">
        <div x-show="loading" class="absolute inset-0 bg-dark z-50 flex items-center justify-center"><span class="text-white animate-pulse">Carregando Analytics...</span></div>
        
        <div x-show="!loading" x-transition>
            <h1 class="text-2xl font-bold text-white mb-6">Dashboard 360º</h1>
            
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-card p-5 rounded-xl border border-slate-700 border-l-4 border-l-blue-500">
                    <p class="text-xs uppercase font-bold text-slate-400">Faturamento</p>
                    <h3 class="text-2xl font-bold text-white">R$ <span x-text="data.kpis?.revenue"></span></h3>
                </div>
                <div class="bg-card p-5 rounded-xl border border-slate-700 border-l-4 border-l-emerald-500">
                    <p class="text-xs uppercase font-bold text-slate-400">Lucro Líquido</p>
                    <h3 class="text-2xl font-bold text-emerald-400">R$ <span x-text="data.kpis?.net"></span></h3>
                </div>
                <div class="bg-card p-5 rounded-xl border border-slate-700 border-l-4 border-l-purple-500">
                    <p class="text-xs uppercase font-bold text-slate-400">Margem Global</p>
                    <h3 class="text-2xl font-bold text-white"><span x-text="data.kpis?.roi"></span>%</h3>
                </div>
                <div class="bg-card p-5 rounded-xl border border-slate-700 border-l-4 border-l-yellow-500">
                    <p class="text-xs uppercase font-bold text-slate-400">Ticket Médio</p>
                    <h3 class="text-2xl font-bold text-white">R$ <span x-text="data.kpis?.ticket"></span></h3>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-card p-6 rounded-xl border border-slate-700">
                    <h4 class="font-bold text-white mb-4">Evolução Financeira</h4>
                    <div class="h-64"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-card p-6 rounded-xl border border-slate-700">
                    <h4 class="font-bold text-white mb-4">Composição de Custos</h4>
                    <div class="h-64"><canvas id="donutChart"></canvas></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function dashboard() {
            return {
                loading: true, data: {},
                init() {
                    fetch('{{ route("dashboard.api") }}').then(r=>r.json()).then(d => {
                        this.data = d; this.loading = false;
                        this.$nextTick(() => this.charts());
                    });
                },
                charts() {
                    Chart.defaults.color = '#94a3b8'; Chart.defaults.borderColor = '#334155';
                    new Chart(document.getElementById('lineChart'), {
                        type: 'line',
                        data: {
                            labels: this.data.timeline.labels,
                            datasets: [{ label: 'Faturamento', data: this.data.timeline.revenue, borderColor: '#3483FA', tension: 0.4 }, { label: 'Lucro', data: this.data.timeline.profit, borderColor: '#10B981', tension: 0.4 }]
                        }, options: { maintainAspectRatio: false }
                    });
                    new Chart(document.getElementById('donutChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Produto', 'Impostos', 'Taxas ML', 'Operacional', 'Lucro'],
                            datasets: [{ data: this.data.donut, backgroundColor: ['#F59E0B', '#EF4444', '#3483FA', '#64748B', '#10B981'], borderWidth: 0 }]
                        }, options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
                    });
                }
            }
        }
    </script>
</body>
</html>