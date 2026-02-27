<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Financeiras | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }</script>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden">
    
    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto p-8">
        <h1 class="text-3xl font-bold text-white mb-8">Parâmetros Financeiros Globais</h1>

        <div class="bg-card border border-slate-700 rounded-2xl p-8 max-w-3xl shadow-xl">
            
            <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg text-sm text-blue-300 flex gap-3">
                <i class="fa-solid fa-circle-info text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold mb-1">Como funciona o cálculo:</p>
                    <p>Defina as taxas globais abaixo. Elas serão aplicadas automaticamente sobre a <b>Venda Bruta</b> de todos os pedidos para gerar a DRE Gerencial real.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('settings.finance.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-white font-bold mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-yellow-500"></i> Impostos (Fiscal)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" name="tax_rate" value="{{ Auth::user()->company->tax_rate }}" class="w-full bg-slate-800 border border-slate-600 text-white rounded-lg p-3 pl-4 text-lg font-mono focus:border-primary focus:outline-none transition">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-bold">%</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Alíquota total da nota fiscal (Ex: DAS, ICMS).</p>
                    </div>

                    <div>
                        <label class="block text-white font-bold mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-purple-500"></i> Custo Operacional
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" name="operational_cost_rate" value="{{ Auth::user()->company->operational_cost_rate }}" class="w-full bg-slate-800 border border-slate-600 text-white rounded-lg p-3 pl-4 text-lg font-mono focus:border-primary focus:outline-none transition">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-bold">%</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Custo fixo estimado (aluguel, equipe, luz) a descontar da margem.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-700">
                    <button type="submit" class="bg-primary hover:bg-cyan-500 text-slate-900 px-6 py-3 rounded-lg font-bold transition flex items-center gap-2 shadow-lg shadow-cyan-500/20">
                        <i class="fa-solid fa-save"></i> Salvar Parâmetros
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>