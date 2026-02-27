<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora 360 | {{ $product->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }
    </script>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen overflow-hidden flex flex-col">

    <header class="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
            <h1 class="text-white font-bold truncate max-w-md">{{ $product->title }}</h1>
            <span class="text-xs bg-slate-800 px-2 py-1 rounded border border-slate-700">{{ $product->sku }}</span>
        </div>
        <div>
            <span class="text-xs text-slate-500">MLB ID: {{ $product->ml_id }}</span>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-6 md:p-10">
        <div class="max-w-6xl mx-auto">
            
            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('products.update', $product->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                
                <div class="space-y-6">
                    <div class="bg-card border border-slate-700 p-6 rounded-2xl shadow-lg">
                        <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-primary"></i> Parâmetros de Precificação
                        </h3>

                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Preço de Venda (R$)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-slate-500 font-bold">R$</span>
                                <input type="number" step="0.01" name="sale_price" value="{{ $product->sale_price }}" class="w-full bg-slate-900 border border-slate-600 rounded-xl py-3 pl-10 pr-4 text-white font-bold focus:border-primary focus:outline-none transition text-lg">
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Alterar aqui recalcula as margens.</p>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Custo do Produto (Bling)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-slate-500 font-bold">R$</span>
                                <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" class="w-full bg-slate-900 border border-slate-600 rounded-xl py-3 pl-10 pr-4 text-white font-bold focus:border-primary focus:outline-none transition">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Estoque Disponível</label>
                            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="w-full bg-slate-900 border border-slate-600 rounded-xl py-3 px-4 text-white focus:border-primary focus:outline-none transition">
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-cyan-500 text-slate-900 font-bold py-4 rounded-xl transition shadow-lg shadow-cyan-500/20 mt-4">
                            <i class="fa-solid fa-calculator"></i> Recalcular Lucro
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-card border border-slate-700 p-8 rounded-2xl shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/10 to-transparent rounded-bl-full -mr-8 -mt-8"></div>
                        
                        <h3 class="text-slate-400 text-sm font-bold uppercase mb-2">Resultado Líquido Estimado</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold {{ $product->profit_value > 0 ? 'text-green-400' : 'text-red-500' }}">
                                R$ {{ number_format($product->profit_value, 2, ',', '.') }}
                            </span>
                            <span class="text-lg {{ $product->profit_margin > 0 ? 'text-green-500' : 'text-red-500' }}">
                                ({{ number_format($product->profit_margin, 2, ',', '.') }}%)
                            </span>
                        </div>

                        <div class="mt-8 space-y-3">
                            <div class="flex justify-between text-sm py-2 border-b border-slate-700/50">
                                <span class="text-slate-400">Faturamento Bruto</span>
                                <span class="text-white font-bold">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm py-2 border-b border-slate-700/50">
                                <span class="text-red-400">(-) Custo do Produto</span>
                                <span class="text-red-400">R$ {{ number_format($product->cost_price, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm py-2 border-b border-slate-700/50">
                                <span class="text-red-400">(-) Comissão (18% Est.)</span>
                                <span class="text-red-400">R$ {{ number_format($product->sale_price * 0.18, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm py-2 border-b border-slate-700/50">
                                <span class="text-red-400">(-) Impostos (10% Est.)</span>
                                <span class="text-red-400">R$ {{ number_format($product->sale_price * 0.10, 2, ',', '.') }}</span>
                            </div>
                            @if($product->sale_price < 79)
                            <div class="flex justify-between text-sm py-2 border-b border-slate-700/50">
                                <span class="text-red-400">(-) Taxa Fixa ML</span>
                                <span class="text-red-400">R$ 5,00</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>