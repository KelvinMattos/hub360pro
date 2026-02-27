<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PrismaHUB - Dashboard</title>
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
                        success: '#00A650', warning: '#F59E0B', danger: '#EF4444'
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-dark text-slate-300">
    <div class="flex h-screen overflow-hidden">
        
        @include('components.sidebar') 

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden p-6">
            
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white">Dashboard <span class="text-primary">PRO</span></h1>
                    <p class="text-sm text-slate-500">Visão unificada das suas operações.</p>
                </div>
                <div class="flex items-center gap-4">
                     <span class="text-xs font-mono text-slate-500">{{ now()->format('d/m/Y') }}</span>
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-bold">
                            <i class="fa-solid fa-power-off"></i> Sair
                        </button>
                     </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-card border border-slate-700 p-6 rounded-xl shadow-lg relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 bg-primary/10 w-24 h-24 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Vendas Hoje</p>
                            <h3 class="text-3xl font-extrabold text-white mt-1">
                                R$ {{ number_format($salesToday ?? 0, 2, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-2 bg-primary/20 rounded-lg text-primary"><i class="fa-solid fa-chart-line text-xl"></i></div>
                    </div>
                    <div class="text-xs text-emerald-400 font-bold">
                        <i class="fa-solid fa-box-open"></i> {{ $ordersCountToday ?? 0 }} pedidos
                    </div>
                </div>

                <div class="bg-card border border-slate-700 p-6 rounded-xl shadow-lg relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 bg-emerald-500/10 w-24 h-24 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Acumulado Mês</p>
                            <h3 class="text-3xl font-extrabold text-white mt-1">
                                R$ {{ number_format($salesMonth ?? 0, 2, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400"><i class="fa-solid fa-calendar-days text-xl"></i></div>
                    </div>
                </div>

                <div class="bg-card border border-slate-700 p-6 rounded-xl shadow-lg relative overflow-hidden cursor-pointer hover:border-yellow-500/50 transition" onclick="window.location='{{ route('inventory.planning') }}'">
                    <div class="absolute -right-6 -top-6 bg-yellow-500/10 w-24 h-24 rounded-full blur-xl"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Planejamento</p>
                            <h3 class="text-xl font-bold text-white mt-1">Ver Estoque</h3>
                        </div>
                        <div class="p-2 bg-yellow-500/20 rounded-lg text-yellow-400"><i class="fa-solid fa-clipboard-list text-xl"></i></div>
                    </div>
                    <p class="text-xs text-primary hover:underline">Acessar reposição ></p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('products.index') }}" class="p-4 bg-slate-800 rounded-lg border border-slate-700 hover:bg-slate-700 transition flex flex-col items-center justify-center gap-2 text-sm font-bold text-white">
                    <i class="fa-solid fa-box text-blue-400 text-xl"></i> Produtos
                </a>
                <a href="{{ route('orders.index') }}" class="p-4 bg-slate-800 rounded-lg border border-slate-700 hover:bg-slate-700 transition flex flex-col items-center justify-center gap-2 text-sm font-bold text-white">
                    <i class="fa-solid fa-truck-fast text-emerald-400 text-xl"></i> Pedidos
                </a>
                <a href="{{ route('settings.integrations') }}" class="p-4 bg-slate-800 rounded-lg border border-slate-700 hover:bg-slate-700 transition flex flex-col items-center justify-center gap-2 text-sm font-bold text-white">
                    <i class="fa-solid fa-plug text-slate-400 text-xl"></i> Integrações
                </a>
            </div>

        </div>
    </div>
</body>
</html>