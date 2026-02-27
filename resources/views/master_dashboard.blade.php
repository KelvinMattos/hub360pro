<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Dashboard | Prisma ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .bg-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
        }

        .text-primary {
            color: #8b5cf6;
        }
    </style>
</head>

<body class="bg-[#0f172a] text-slate-300 min-h-screen">

    <div class="flex">
        <!-- Sidebar Simplificada para Admin -->
        <div class="w-64 bg-slate-900 h-screen sticky top-0 border-r border-slate-800 p-6">
            <div class="flex items-center gap-3 mb-10">
                <div
                    class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                    <i class="fa-solid fa-cube text-white"></i>
                </div>
                <span class="text-white font-bold text-xl tracking-tight">Prisma <span
                        class="text-purple-500">ERP</span></span>
            </div>

            <nav class="space-y-1">
                <a href="{{ url('/admin') }}"
                    class="flex items-center gap-3 bg-purple-600/10 text-purple-400 p-3 rounded-lg border border-purple-500/20 font-medium">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ url('/admin/integrations') }}"
                    class="flex items-center gap-3 text-slate-400 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                    <i class="fa-solid fa-plug"></i> Integracoes
                </a>
            </nav>
        </div>

        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Configuracoes Base</h1>
                        <p class="text-slate-500 mt-1">Gerencie chaves de IA e parametros globais do sistema.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div class="space-y-6">
                        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700">
                            <h3 class="text-white font-bold mb-4 flex items-center gap-2"><i
                                    class="fa-solid fa-key text-yellow-400"></i> Adicionar Nova Chave</h3>
                            <form action="{{ route('admin.store_key') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">Provedor</label>
                                        <select name="provider"
                                            class="w-full bg-slate-900 border border-slate-600 rounded-lg p-2 text-white text-sm focus:border-purple-500 focus:outline-none">
                                            <option value="gemini">Google Gemini (Recomendado)</option>
                                            <option value="openai">OpenAI (GPT-4)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] uppercase font-bold text-slate-500">API Key</label>
                                        <input type="text" name="api_key" placeholder="Cole sua chave aqui..."
                                            class="w-full bg-slate-900 border border-slate-600 rounded-lg p-2 text-white text-sm focus:border-purple-500 focus:outline-none">
                                    </div>
                                    <button type="submit"
                                        class="bg-purple-600 hover:bg-purple-500 text-white font-bold py-2 rounded-lg transition shadow-lg shadow-purple-500/20">
                                        <i class="fa-solid fa-plus mr-1"></i> Salvar Chave
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-card border border-slate-700 rounded-xl overflow-hidden">
                            <div class="p-4 bg-slate-800/50 border-b border-slate-700 font-bold text-slate-300 text-sm">
                                Chaves Ativas</div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse ($aiKeys as $key)
                                <div
                                    class="flex justify-between items-center p-4 border-b border-slate-800 hover:bg-slate-800/30 transition last:border-0">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $key->provider == 'gemini' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400' }}">{{
                                                $key->provider }}</span>
                                            <span class="text-white font-mono text-xs">...{{ substr($key->api_key, -5)
                                                }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-1 flex gap-3">
                                            <span
                                                class="{{ $key->error_count > 0 ? 'text-red-400' : 'text-slate-500' }}">Erros:
                                                {{ $key->error_count }}</span>
                                            <span>Uso: {{ $key->last_used_at ?
                                                \Carbon\Carbon::parse($key->last_used_at)->diffForHumans() : 'Nunca'
                                                }}</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.delete_key', $key->id) }}" method="POST"
                                        onsubmit="return confirm('Remover esta chave?')">
                                        @csrf @method('DELETE')
                                        <button class="text-slate-500 hover:text-red-500 p-2"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                                @empty
                                <div class="p-6 text-center text-slate-500 text-sm">Nenhuma chave cadastrada. O sistema
                                    requer chaves de IA.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 h-full flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-white font-bold flex items-center gap-2"><i
                                        class="fa-solid fa-database text-blue-400"></i> Taxas de Mercado</h3>
                                <p class="text-[10px] text-slate-400">Copia Dourada obtida via IA.</p>
                            </div>
                            <form action="{{ route('admin.force_update') }}" method="POST">
                                @csrf
                                <button
                                    class="text-xs bg-slate-700 hover:bg-white hover:text-slate-900 text-white px-4 py-2 rounded-lg font-bold transition flex items-center gap-2 border border-slate-600">
                                    <i class="fa-solid fa-rotate"></i> Forcar Scan
                                </button>
                            </form>
                        </div>

                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead class="text-slate-500 border-b border-slate-700 text-[10px] uppercase">
                                    <tr>
                                        <th class="pb-2 pl-2">Plataforma</th>
                                        <th class="pb-2">Tipo</th>
                                        <th class="pb-2">Comissao</th>
                                        <th class="pb-2">Fixo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700/50">
                                    @foreach ($aiRates as $rate)
                                    <tr>
                                        <td class="py-3 pl-2 text-white font-bold capitalize">
                                            @if(str_contains($rate->platform, 'mercadolibre')) <i
                                                class="fa-solid fa-handshake text-yellow-400 mr-1"></i> Mercado Livre
                                            @elseif(str_contains($rate->platform, 'shopee')) <i
                                                class="fa-solid fa-bag-shopping text-orange-500 mr-1"></i> Shopee
                                            @else {{ $rate->platform }} @endif
                                        </td>
                                        <td class="py-3 text-slate-400 capitalize">{{ $rate->listing_type }}</td>
                                        <td class="py-3 text-emerald-400 font-bold">{{ $rate->commission_percent }}%
                                        </td>
                                        <td class="py-3 text-slate-300">R$ {{ number_format($rate->fixed_fee, 2, ',',
                                            '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-700 text-center">
                            <p class="text-[10px] text-slate-500">
                                Ultima verificacao: <span class="text-slate-300">{{ $aiRates->first()->last_check_at ??
                                    'Nunca' }}</span>
                                via <span class="text-purple-400 font-bold">{{ $aiRates->first()->updated_via ?? '-'
                                    }}</span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>

</html>