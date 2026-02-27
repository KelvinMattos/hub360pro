<form method="GET" action="{{ route('products.index') }}">
    <input type="hidden" name="search" value="{{ request('search') }}">

    <div class="space-y-6">
        <div class="grid grid-cols-2 gap-2 text-center text-xs mb-4">
            <div class="bg-emerald-500/10 text-emerald-400 p-2 rounded border border-emerald-500/20">
                <span class="block font-bold text-lg">{{ $stats['active'] ?? 0 }}</span>
                Ativos
            </div>
            <div class="bg-yellow-500/10 text-yellow-500 p-2 rounded border border-yellow-500/20">
                <span class="block font-bold text-lg">{{ $stats['paused'] ?? 0 }}</span>
                Pausados
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Status</h3>
            <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="" class="accent-primary" {{ request('status') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="text-sm">Todos</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="active" class="accent-primary" {{ request('status') == 'active' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="text-sm">Ativos</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="paused" class="accent-primary" {{ request('status') == 'paused' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="text-sm">Pausados</span>
                </label>
            </div>
        </div>

        <div class="border-t border-slate-700"></div>

        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Exposição</h3>
            <select name="listing_type" class="w-full bg-dark border border-slate-600 rounded p-2 text-sm text-white" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="gold_special" {{ request('listing_type') == 'gold_special' ? 'selected' : '' }}>Clássico</option>
                <option value="gold_pro" {{ request('listing_type') == 'gold_pro' ? 'selected' : '' }}>Premium</option>
            </select>
        </div>

        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Alertas</h3>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="low_stock" value="1" class="accent-red-500" {{ request('low_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                <span class="text-sm text-red-400">Estoque Baixo (<5)</span>
            </label>
        </div>
    </div>
</form>