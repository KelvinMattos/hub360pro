@props(['product'])

<div id="pricing-card-container-{{ $product->id }}" class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-lg transition-all duration-500 relative">
    
    <div id="pricing-loader-{{ $product->id }}" class="absolute inset-0 bg-slate-900/80 z-10 hidden flex-col items-center justify-center backdrop-blur-sm">
        <i class="fa-solid fa-circle-notch fa-spin text-primary text-2xl mb-2"></i>
        <span class="text-xs text-slate-300 font-bold animate-pulse">Calculando Taxas em Tempo Real...</span>
    </div>

    <div class="bg-slate-900/50 p-4 border-b border-slate-700 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-hand-holding-dollar text-green-400"></i>
            <h3 class="text-white font-bold text-sm">Custos de Marketplace</h3>
        </div>
        
        <button 
            type="button"
            onclick="triggerMeliSync({{ $product->id }})" 
            class="group text-[10px] bg-slate-700 hover:bg-primary hover:text-slate-900 text-slate-300 px-3 py-1.5 rounded transition flex items-center gap-2 border border-slate-600 hover:border-primary">
            <i class="fa-solid fa-rotate group-hover:rotate-180 transition-transform duration-500"></i> 
            <span>Recalcular</span>
        </button>
    </div>

    <div class="p-4 space-y-3">
        
        <div class="flex justify-between text-sm">
            <span class="text-slate-400">Categoria:</span>
            <span class="text-white font-mono text-xs bg-slate-700 px-1.5 py-0.5 rounded">
                {{ $product->category_id ?? '---' }}
            </span>
        </div>

        <div class="border-t border-slate-700 my-2"></div>

        <div class="flex justify-between text-sm">
            <span class="text-slate-400">
                Comissão ({{ $product->listing_type_id == 'gold_pro' ? 'Premium' : 'Clássico' }})
            </span>
            <div class="text-right">
                @php
                    $pct = $product->pricing->meli_commission_percent;
                    $price = $product->sale_price;
                    $commValue = $price * ($pct / 100);
                @endphp
                <span class="text-red-400 font-bold">
                    R$ {{ number_format($commValue, 2, ',', '.') }}
                </span>
                <span class="text-xs text-slate-500 block">
                    ({{ number_format($pct, 2) }}%)
                </span>
            </div>
        </div>

        @if($product->pricing->fixed_costs_extra > 0)
        <div class="flex justify-between text-sm bg-yellow-500/10 p-2 rounded border border-yellow-500/20">
            <span class="text-yellow-500 font-bold flex items-center gap-1">
                <i class="fa-solid fa-tag"></i> Taxa Fixa (< R$79)
            </span>
            <span class="text-yellow-400 font-bold">
                R$ {{ number_format($product->pricing->fixed_costs_extra, 2, ',', '.') }}
            </span>
        </div>
        @endif

        <div class="flex justify-between text-sm items-center">
            <span class="text-slate-400 flex items-center gap-1">
                Frete Estimado
                @if($product->sale_price < 79)
                    <i class="fa-solid fa-check-circle text-success text-xs" title="Pago pelo Comprador"></i>
                @endif
            </span>
            <span class="{{ $product->pricing->shipping_cost > 0 ? 'text-red-400' : 'text-slate-500' }} font-bold">
                R$ {{ number_format($product->pricing->shipping_cost, 2, ',', '.') }}
            </span>
        </div>

        <div class="border-t border-slate-700 my-2"></div>

        <div class="flex justify-between items-end">
            <span class="text-slate-300 font-bold text-sm">Custo Total</span>
            @php
                $totalFees = $commValue + $product->pricing->fixed_costs_extra + $product->pricing->shipping_cost;
            @endphp
            <div class="text-right">
                <span class="text-xl text-white font-bold block leading-none">
                    R$ {{ number_format($totalFees, 2, ',', '.') }}
                </span>
                <span class="text-[10px] text-slate-500">
                    {{ number_format(($totalFees / ($price > 0 ? $price : 1)) * 100, 1) }}% da venda
                </span>
            </div>
        </div>
    </div>
</div>

@once
<script>
    // Agora exposta globalmente para ser chamada pelo index.blade.php
    window.triggerMeliSync = function(productId, isAuto = false) {
        const container = document.getElementById(`pricing-card-container-${productId}`);
        const loader = document.getElementById(`pricing-loader-${productId}`);
        const btn = container.querySelector('button');
        const originalContent = btn.innerHTML;
        
        // UI State
        btn.disabled = true;
        if (!isAuto) {
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ...';
            container.classList.add('opacity-50');
        } else {
            // Se for auto, mostra o overlay legal
            loader.classList.remove('hidden');
            loader.classList.add('flex');
        }

        fetch('{{ route("meli.sync_fees") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.new_card_html) {
                container.outerHTML = data.new_card_html;
                if(!isAuto) {
                    // Feedback visual só no manual
                    // alert('Taxas atualizadas!'); 
                }
            } else {
                if(!isAuto) alert(data.message || 'Erro');
                resetBtn();
            }
        })
        .catch(error => {
            console.error(error);
            resetBtn();
        });

        function resetBtn() {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            container.classList.remove('opacity-50');
            loader.classList.add('hidden');
            loader.classList.remove('flex');
        }
    }
</script>
@endonce