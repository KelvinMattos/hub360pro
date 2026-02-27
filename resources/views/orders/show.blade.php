<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venda #{{ $order->external_id }} | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: '#0F172A',       
                        darker: '#020617',     
                        card: '#1E293B',       
                        border: '#334155',     
                        primary: '#06B6D4',    
                        success: '#10B981',    
                        warning: '#F59E0B',    
                        danger: '#EF4444',     
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Fira Code', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        [x-cloak] { display: none !important; }
        
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden" 
      x-data="{ 
          showJson: false,
          financialModal: false,
          selectedItem: {}, 
          
          openFinancial(item) {
              this.selectedItem = item;
              
              // Garante numéricos para o JS não quebrar
              this.selectedItem.unit_price = parseFloat(item.unit_price || 0);
              this.selectedItem.unit_cost = parseFloat(item.unit_cost || 0);
              
              // Define taxa padrão visual caso não tenha vindo do backend
              if(!this.selectedItem.tax_rate) this.selectedItem.tax_rate = 0.18; 
              
              this.financialModal = true;
          }
      }">

    @include('components.sidebar')

    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 z-20 shadow-md">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2 text-sm font-medium group">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center group-hover:bg-primary group-hover:text-slate-900 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <span class="hidden sm:inline">Voltar</span>
                </a>
                
                <div class="h-8 w-px bg-border mx-2"></div>
                
                <div>
                    <div class="flex items-center gap-2">
                        <span class="bg-[#FFE600] text-slate-900 text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">MELI</span>
                        <h1 class="text-white font-bold text-lg leading-none">Venda #{{ $order->external_id }}</h1>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        {{ $order->date_created->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="px-3 py-1 rounded-full border border-slate-700 bg-slate-800 text-xs font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $order->status == 'paid' ? 'bg-success' : 'bg-warning' }}"></span>
                    {{ strtoupper($order->status) }}
                </div>

                <form action="{{ route('orders.sync_single', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-primary hover:bg-cyan-400 text-slate-900 px-4 py-2 rounded-lg text-sm font-bold transition shadow-lg shadow-cyan-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-rotate"></i> <span class="hidden md:inline">Atualizar</span>
                    </button>
                </form>
                
                @if($order->shipping_id)
                <a href="{{ route('orders.label', $order->id) }}" target="_blank" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition" title="Imprimir Etiqueta">
                    <i class="fa-solid fa-print"></i>
                </a>
                @endif
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 scroll-smooth">
            
            @if(session('success'))
                <div class="max-w-7xl mx-auto mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <div>
                        <h4 class="font-bold text-sm">Atualizado!</h4>
                        <p class="text-xs">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-card border border-border rounded-xl p-5 shadow-lg relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="fa-solid fa-user text-8xl text-slate-400"></i>
                        </div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center text-xl font-bold text-slate-300 border-2 border-slate-600">
                                {{ substr($order->customer_name ?? 'C', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">{{ $order->customer_name }}</h3>
                                <div class="flex items-center gap-3 text-sm text-slate-400 mt-1">
                                    <span class="bg-slate-800 px-2 py-0.5 rounded text-xs font-mono border border-slate-700">
                                        {{ $order->customer_doc ?? 'CPF N/D' }}
                                    </span>
                                    @if($order->buyer_nickname)
                                        <span class="flex items-center gap-1"><i class="fa-solid fa-tag"></i> {{ $order->buyer_nickname }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-card border border-border rounded-xl p-5 shadow-lg">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 shrink-0">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm mb-1 flex items-center gap-2">
                                    Logística de Envio
                                    <span class="text-[10px] bg-slate-700 text-slate-300 px-2 py-0.5 rounded border border-slate-600 uppercase">
                                        {{ $order->logistic_type ?? 'Correios' }}
                                    </span>
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    {{ $order->shipping_address_line }}<br>
                                    {{ $order->shipping_neighborhood }} - {{ $order->shipping_city }}/{{ $order->shipping_state }}<br>
                                    <span class="text-slate-500 font-mono text-xs">CEP: {{ $order->shipping_zip }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-card border border-border rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-slate-800/50 px-5 py-3 border-b border-border flex justify-between items-center">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Produtos do Pedido</h3>
                            <span class="text-xs text-slate-500">{{ $order->items->count() }} item(s)</span>
                        </div>
                        
                        <div class="divide-y divide-border">
                            @foreach($order->items as $item)
                            <div class="p-4 flex gap-4 hover:bg-slate-800/50 transition relative group">
                                <div class="w-16 h-16 bg-slate-800 rounded-lg border border-border flex items-center justify-center overflow-hidden shrink-0">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-regular fa-image text-slate-600 text-xl"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <button 
                                        type="button"
                                        @click="openFinancial({
                                            title: '{{ addslashes($item->title) }}',
                                            sku: '{{ $item->sku }}',
                                            unit_price: {{ $item->unit_price }},
                                            unit_cost: {{ $item->product->cost_price ?? ($item->unit_cost ?? 0) }},
                                            product_found: {{ $item->product ? 'true' : 'false' }},
                                            category_id: '{{ $item->product->category_id ?? '' }}'
                                        })"
                                        class="text-left text-base font-bold text-white hover:text-primary transition-colors line-clamp-2 leading-tight mb-1"
                                    >
                                        {{ $item->title }}
                                    </button>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-mono text-slate-500 bg-slate-900 px-1.5 py-0.5 rounded border border-slate-700">
                                            SKU: {{ $item->sku }}
                                        </span>
                                        
                                        @if(!$item->product)
                                            <span class="text-[10px] text-red-400 bg-red-500/10 px-2 py-0.5 rounded border border-red-500/20 flex items-center gap-1">
                                                <i class="fa-solid fa-link-slash"></i> Não Vinculado
                                            </span>
                                        @else
                                            <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 flex items-center gap-1 cursor-help" title="Custo e Taxas Sincronizados">
                                                <i class="fa-solid fa-link"></i> Vinculado
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="block text-white font-bold">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                    <span class="block text-xs text-slate-500">x{{ $item->quantity }} un.</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="space-y-6">
                    <div class="bg-darker border border-border rounded-xl shadow-xl overflow-hidden relative">
                        <div class="bg-slate-800/80 p-4 border-b border-border flex justify-between items-center backdrop-blur-sm">
                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-calculator text-primary"></i> DRE do Pedido
                            </h3>
                        </div>

                        <div class="p-5 space-y-3 font-mono text-sm relative z-10">
                            <div class="flex justify-between items-center text-emerald-400">
                                <span class="font-bold flex items-center gap-2"><i class="fa-solid fa-plus-circle text-[10px]"></i> Receita Bruta</span>
                                <span class="font-bold text-lg">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                            </div>

                            <div class="space-y-1 pl-4 border-l-2 border-slate-800">
                                <div class="flex justify-between text-xs text-red-400/80">
                                    <span>(-) Impostos</span>
                                    <span>- R$ {{ number_format($order->cost_tax_fiscal, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-red-400 font-bold">
                                    <span>(-) Taxas Marketplace</span>
                                    <span>- R$ {{ number_format($order->cost_tax_platform, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="border-b border-slate-800 my-2"></div>

                            <div class="flex justify-between items-center text-blue-300">
                                <span>(=) Margem Contrib.</span>
                                <span class="font-bold">R$ {{ number_format($order->contribution_margin, 2, ',', '.') }}</span>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-orange-400/80">
                                    <span>(-) Custo Operacional</span>
                                    <span>- R$ {{ number_format($order->cost_operational, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-orange-500 font-bold">
                                    <span>(-) Custo Produto (CMV)</span>
                                    <span>- R$ {{ number_format($order->cost_products, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-800/50 p-4 -mx-2 rounded-lg border border-slate-700/50 mt-4">
                                <div class="flex justify-between items-end">
                                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Lucro Líquido</span>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold {{ $order->net_profit > 0 ? 'text-emerald-400' : 'text-red-500' }}">
                                            R$ {{ number_format($order->net_profit, 2, ',', '.') }}
                                        </div>
                                        <div class="text-xs {{ $order->net_profit > 0 ? 'text-emerald-600' : 'text-red-700' }} font-bold">
                                            Margem: {{ $order->total_amount > 0 ? number_format(($order->net_profit / $order->total_amount)*100, 1) : 0 }}%
                                        </div>
                                    </div>
                                </div>
                                @php 
                                    $percent = $order->total_amount > 0 ? ($order->net_profit / $order->total_amount) * 100 : 0;
                                    $percent = max(0, min(100, $percent));
                                @endphp
                                <div class="w-full bg-slate-900 h-1.5 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 {{ $order->net_profit > 0 ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button @click="showJson = !showJson" class="w-full text-center text-xs text-slate-600 hover:text-slate-400 transition uppercase font-bold tracking-widest">
                        <i class="fa-solid fa-code"></i> Debug API <span x-text="showJson ? '[-]' : '[+]'"></span>
                    </button>
                    
                    <div x-show="showJson" x-cloak class="bg-black rounded-lg p-4 text-[10px] font-mono text-green-500 h-64 overflow-auto border border-slate-800">
                        {{ json_encode($order->json_order, JSON_PRETTY_PRINT) }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div x-show="financialModal" 
         x-cloak 
         class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/90 backdrop-blur-sm p-4 fade-in"
         @click.away="financialModal = false"
         @keydown.escape.window="financialModal = false">
         
        <div class="bg-card border border-slate-600 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative">
            
            <div class="bg-slate-900 p-5 border-b border-slate-700 flex justify-between items-start">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-500/10 text-yellow-500 flex items-center justify-center border border-yellow-500/20 shrink-0">
                        <i class="fa-solid fa-magnifying-glass-dollar text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg leading-tight">Raio-X do Item</h3>
                        <p class="text-xs text-slate-400 mt-1">Análise de rentabilidade unitária</p>
                    </div>
                </div>
                <button @click="financialModal = false" class="text-slate-500 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="pb-4 border-b border-slate-700/50">
                    <h4 class="text-sm font-semibold text-white leading-snug" x-text="selectedItem.title"></h4>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-slate-700 font-mono">
                            SKU: <span x-text="selectedItem.sku"></span>
                        </span>
                        <template x-if="!selectedItem.product_found">
                            <span class="text-[10px] text-red-400 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Custo Estimado</span>
                        </template>
                    </div>
                </div>
                
                <div class="bg-slate-950/50 rounded-xl p-4 border border-slate-700/50 space-y-3 text-sm font-mono shadow-inner">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Preço de Venda</span>
                        <span class="text-white font-bold text-base">R$ <span x-text="selectedItem.unit_price.toFixed(2)"></span></span>
                    </div>
                    
                    <div class="flex justify-between text-orange-400 border-l-2 border-orange-500/20 pl-2">
                        <span>(-) Custo Reposição</span>
                        <span>R$ <span x-text="selectedItem.unit_cost.toFixed(2)"></span></span>
                    </div>
                    
                    <div class="flex justify-between text-red-400 border-l-2 border-red-500/20 pl-2">
                        <span>(-) Taxas + Impostos (Est.)</span>
                        <span>R$ <span x-text="(selectedItem.unit_price * 0.24).toFixed(2)"></span></span>
                    </div>
                    
                    <div class="border-t border-slate-700 my-2 pt-2"></div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-emerald-400 font-bold uppercase text-xs">Lucro Unitário</span>
                        <span class="text-emerald-400 font-bold text-lg">
                            R$ <span x-text="(selectedItem.unit_price - selectedItem.unit_cost - (selectedItem.unit_price * 0.24)).toFixed(2)"></span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3">
                        <h5 class="text-blue-400 font-bold text-[10px] uppercase mb-1">Preço Ideal (25%)</h5>
                        <span class="text-white font-bold text-lg block">
                            R$ <span x-text="(selectedItem.unit_cost * 1.8).toFixed(2)"></span>
                        </span>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-3">
                        <h5 class="text-slate-400 font-bold text-[10px] uppercase mb-1">Média Concorrente</h5>
                        <span class="text-white font-bold text-lg block opacity-50">---</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-900 border-t border-slate-700 flex justify-end gap-2">
                 <a x-bind:href="'/products?search=' + selectedItem.sku" class="px-4 py-2 text-slate-400 hover:text-white text-sm transition">Editar Produto</a>
                <button @click="financialModal = false" class="px-6 py-2 bg-primary hover:bg-cyan-400 text-slate-900 text-sm font-bold rounded-lg transition shadow-lg shadow-cyan-500/20">OK</button>
            </div>
        </div>
    </div>

</body>
</html>