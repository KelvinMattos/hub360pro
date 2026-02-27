<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | PrismaHUB</title>
    
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
                        warning: '#F59E0B', danger: '#EF4444'
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
        .table-row-hover:hover td { background-color: rgba(52, 131, 250, 0.05); }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden">

    @include('components.sidebar')

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-card border-b border-slate-700 p-4 z-20 shadow-md">
            <div class="max-w-[1920px] mx-auto flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-users text-primary"></i> Carteira de Clientes
                    </h1>
                    <div class="text-xs text-slate-400 font-mono bg-slate-800 px-2 py-1 rounded border border-slate-700">
                        Total: {{ $customers->total() }}
                    </div>
                </div>

                <form method="GET" action="{{ route('customers.index') }}" class="relative group">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 group-focus-within:text-primary transition"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por Nome ou CPF/CNPJ..." 
                           class="w-full bg-dark border border-slate-600 rounded-xl pl-12 pr-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition shadow-inner font-medium">
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-hidden flex">
            <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-dark custom-scrollbar">
                
                @if(session('info'))
                    <div class="mb-4 p-4 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg text-sm flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                @if($customers->count() > 0)
                <div class="bg-card border border-slate-700 rounded-xl overflow-hidden shadow-xl">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-800 text-slate-400 text-xs uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-4 w-16 text-center"><i class="fa-solid fa-user-tag"></i></th>
                                <th class="p-4">Cliente / Documento</th>
                                <th class="p-4 text-center">Pedidos</th>
                                <th class="p-4 text-right">LTV (Total Gasto)</th>
                                <th class="p-4 text-right">Última Compra</th>
                                <th class="p-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @foreach($customers as $c)
                            <tr class="table-row-hover transition group cursor-pointer" 
                                onclick="window.location='{{ route('customers.show', $c->billing_doc_number) }}'">
                                
                                <td class="p-4 text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-600 flex items-center justify-center mx-auto text-slate-400 font-bold uppercase">
                                        {{ substr($c->customer_name, 0, 1) }}
                                    </div>
                                </td>
                                
                                <td class="p-4">
                                    <div class="font-bold text-white text-base group-hover:text-primary transition">{{ $c->customer_name }}</div>
                                    <div class="text-xs text-slate-500 font-mono mt-1 flex items-center gap-2">
                                        <i class="fa-regular fa-id-card"></i> {{ $c->billing_doc_number }}
                                    </div>
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span class="bg-slate-800 text-white px-2.5 py-1 rounded-lg border border-slate-600 font-mono text-xs font-bold">
                                        {{ $c->total_orders }}
                                    </span>
                                </td>
                                
                                <td class="p-4 text-right font-bold text-emerald-400">
                                    R$ {{ number_format($c->total_spent, 2, ',', '.') }}
                                </td>

                                <td class="p-4 text-right text-xs text-slate-400">
                                    <div class="font-bold text-slate-300">{{ \Carbon\Carbon::parse($c->last_purchase)->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-600">{{ \Carbon\Carbon::parse($c->last_purchase)->diffForHumans() }}</div>
                                </td>

                                <td class="p-4 text-right">
                                    <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-white transition"></i>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 px-2">
                    {{ $customers->appends(request()->query())->links() }}
                </div>

                @else
                <div class="h-96 flex flex-col items-center justify-center text-slate-500 bg-card/50 rounded-xl border-2 border-dashed border-slate-700 m-4">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-4 border border-slate-600">
                        <i class="fa-solid fa-users-slash text-4xl opacity-50"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-400">Nenhum cliente encontrado</h3>
                    <p class="text-sm mt-2">Nenhum pedido com documento vinculado foi encontrado.</p>
                </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>