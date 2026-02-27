<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrações | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script> 

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: '#0F172A', card: '#1E293B', primary: '#3483FA', success: '#00A650',
                        warning: '#F59E0B', danger: '#EF4444', meli: '#FFE600'
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['Fira Code', 'monospace'] }
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
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" x-data="{ mlModal: false }">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-8 relative">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Central de Configurações</h1>
                <p class="text-slate-400">Gerencie regras financeiras e integrações.</p>
            </div>
            
            <a href="{{ route('settings.logs') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 border border-slate-700 shadow-md">
                <i class="fa-solid fa-terminal text-primary"></i> Logs do Sistema
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-6">
                
                <div class="bg-card border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
                    <div class="bg-[#FFE600] p-6 flex justify-between items-center text-black">
                        <div class="flex items-center gap-4">
                            <i class="fa-solid fa-handshake text-3xl"></i>
                            <div>
                                <h3 class="text-xl font-extrabold">Mercado Livre</h3>
                                <p class="text-xs font-bold opacity-80 uppercase">Integração Oficial</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if(isset($integrations['mercadolibre']))
                                <span class="bg-black/20 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div> Conectado
                                </span>
                                <form action="{{ route('settings.delete_integration', $integrations['mercadolibre']->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desconectar?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-black/10 hover:bg-red-500 hover:text-white rounded transition text-black" title="Desconectar">
                                        <i class="fa-solid fa-unlink"></i>
                                    </button>
                                </form>
                            @else
                                <span class="bg-black/20 px-3 py-1 rounded-full text-xs font-bold">Pendente</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        @if(isset($integrations['mercadolibre']))
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-slate-800 p-3 rounded border border-slate-700">
                                        <label class="text-[10px] uppercase text-slate-500 font-bold block mb-1">Seller ID</label>
                                        <code class="text-white font-mono text-xs">{{ $integrations['mercadolibre']->seller_id ?? '---' }}</code>
                                    </div>
                                    <div class="bg-slate-800 p-3 rounded border border-slate-700">
                                        <label class="text-[10px] uppercase text-slate-500 font-bold block mb-1">Status do Token</label>
                                        <code class="text-emerald-400 font-mono text-xs">Válido</code>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button @click="mlModal = true" class="w-full text-center bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 rounded-lg transition border border-slate-600 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-gear"></i> Configurar Credenciais
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-slate-400 mb-6">Insira suas credenciais de aplicação do Mercado Livre para conectar.</p>
                                <button @click="mlModal = true" class="inline-flex items-center justify-center bg-[#2D3277] hover:bg-[#2D3277]/90 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg gap-3">
                                    <i class="fa-solid fa-plug"></i> Conectar Agora
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-card border border-slate-700 rounded-2xl overflow-hidden shadow-xl opacity-60 grayscale">
                    <div class="bg-slate-800 p-6 flex justify-between items-center border-b border-slate-700">
                        <div class="flex items-center gap-4">
                            <i class="fa-solid fa-cube text-3xl text-blue-400"></i>
                            <div>
                                <h3 class="text-xl font-bold text-white">Tiny ERP</h3>
                                <p class="text-xs text-slate-400 uppercase">Em Breve</p>
                            </div>
                        </div>
                        <span class="bg-slate-700 px-3 py-1 rounded-full text-xs font-bold text-slate-400">Indisponível</span>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-4">
                <div class="bg-card border border-slate-700 rounded-2xl shadow-xl sticky top-6">
                    <div class="bg-slate-800 p-5 border-b border-slate-700">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-primary"></i> Regras de Custo
                        </h3>
                    </div>
                    
                    <form action="{{ route('settings.finance.update') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-400 mb-2">Imposto Global (%)</label>
                            <div class="relative">
                                <input type="number" step="0.1" name="tax_rate" value="{{ auth()->user()->company->tax_rate ?? 6.0 }}" 
                                       class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-4 pr-10 text-white focus:border-primary outline-none transition font-bold">
                                <span class="absolute right-4 top-3 text-slate-500">%</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-400 mb-2">Custo Operacional (%)</label>
                            <div class="relative">
                                <input type="number" step="0.1" name="operational_rate" value="{{ auth()->user()->company->operational_rate ?? 10.0 }}" 
                                       class="w-full bg-dark border border-slate-600 rounded-lg py-3 pl-4 pr-10 text-white focus:border-primary outline-none transition font-bold">
                                <span class="absolute right-4 top-3 text-slate-500">%</span>
                            </div>
                        </div>

                        <hr class="border-slate-700">

                        <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition shadow-lg">
                            Salvar Regras
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div x-show="mlModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-card border border-slate-600 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="mlModal = false">
            <div class="bg-[#FFE600] p-4 flex justify-between items-center text-black">
                <h3 class="font-bold text-lg">Configurar Mercado Livre</h3>
                <button @click="mlModal = false" class="hover:bg-black/10 rounded p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="{{ route('settings.update_keys', 'mercadolibre') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1 uppercase">App ID</label>
                    <input type="text" name="app_id" placeholder="Ex: 567890123" required 
                           value="{{ $integrations['mercadolibre']->app_id ?? '' }}"
                           class="w-full bg-dark border border-slate-600 rounded-lg p-3 text-white focus:border-[#FFE600] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1 uppercase">Client Secret</label>
                    <input type="password" name="client_secret" placeholder="Ex: aB1cD2eF3..." required 
                           value="{{ $integrations['mercadolibre']->client_secret ?? '' }}"
                           class="w-full bg-dark border border-slate-600 rounded-lg p-3 text-white focus:border-[#FFE600] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1 uppercase">Redirect URI (Callback)</label>
                    <input type="text" readonly value="{{ route('webhook.handle', 'mercadolibre') }}" 
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-slate-500 text-xs select-all">
                </div>
                
                <button type="submit" class="w-full bg-[#2D3277] hover:bg-[#2D3277]/90 text-white font-bold py-3 rounded-lg transition mt-2">
                    Salvar e Conectar
                </button>
            </form>
        </div>
    </div>

</body>
</html>