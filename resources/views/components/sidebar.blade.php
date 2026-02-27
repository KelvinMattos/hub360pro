<aside class="w-64 bg-card border-r border-slate-700 flex flex-col h-full hidden md:flex z-50">
    <div class="h-16 flex items-center px-6 border-b border-slate-700 shrink-0">
        <h1 class="text-xl font-bold text-white tracking-wider">
            <span class="text-primary">Prisma</span>HUB <span class="text-[10px] text-yellow-500 align-top">PRO</span>
        </h1>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar">
        <ul class="space-y-1 px-3">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-all group">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    <span class="font-medium text-sm">Dashboard 360</span>
                </a>
            </li>

            <li class="px-3 pt-5 pb-2 text-[10px] uppercase font-bold text-yellow-500 tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-crown"></i> Inteligência Pro
            </li>
            <li>
                <a href="{{ route('meli.war_room') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('meli.war_room') ? 'bg-yellow-500/10 text-yellow-500' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-crosshairs w-5 text-center"></i>
                    <span class="font-medium text-sm">Monitor de Guerra</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inventory.planning') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.planning') ? 'bg-yellow-500/10 text-yellow-500' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-boxes-packing w-5 text-center"></i>
                    <span class="font-medium text-sm">Planejamento Estoque</span>
                </a>
            </li>
            <li>
                <a href="{{ route('meli.calculator') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('meli.calculator') ? 'bg-yellow-500/10 text-yellow-500' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-calculator w-5 text-center"></i>
                    <span class="font-medium text-sm">Calculadora Real</span>
                </a>
            </li>

            <li class="px-3 pt-5 pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Operação</li>
            <li>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-box-open w-5 text-center"></i>
                    <span class="font-medium text-sm">Pedidos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span class="font-medium text-sm">Produtos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span class="font-medium text-sm">Clientes</span>
                </a>
            </li>
            
            <li class="px-3 pt-5 pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Sistema</li>
            <li>
                <a href="{{ route('settings.integrations') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <i class="fa-solid fa-sliders w-5 text-center"></i>
                    <span class="font-medium text-sm">Configurações</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="p-4 border-t border-slate-700">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-slate-400 hover:text-red-400 transition text-xs w-full group justify-center">
                <i class="fa-solid fa-arrow-right-from-bracket group-hover:translate-x-1 transition-transform"></i>
                <span>Sair do Sistema</span>
            </button>
        </form>
    </div>
</aside>