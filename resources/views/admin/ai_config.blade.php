<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | AI Config</title>
    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <link href="[https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css)" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#06B6D4' } } } }</script>
</head>
<body class="bg-dark text-slate-300 font-sans p-10">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-brain text-purple-500"></i> PrismaHUB Intelligence
            </h1>
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white">Voltar ao App</a>
        </div>

        @if(session('success')) <div class="bg-green-500/20 text-green-400 p-4 rounded mb-6">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="bg-red-500/20 text-red-400 p-4 rounded mb-6">{{ session('error') }}</div> @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-card border border-slate-700 p-6 rounded-2xl">
                <h3 class="text-white font-bold mb-4">Gerenciamento de Keys (Redundância)</h3>
                
                <form action="{{ route('admin.store_key') }}" method="POST" class="mb-6 bg-slate-800 p-4 rounded-xl">
                    @csrf
                    <div class="grid grid-cols-1 gap-3">
                        <select name="provider" class="bg-slate-900 border border-slate-600 rounded p-2 text-white">
                            <option value="gemini">Google Gemini</option>
                            <option value="openai">OpenAI (GPT-4)</option>
                        </select>
                        <input type="text" name="api_key" placeholder="Cole a API Key aqui..." class="bg-slate-900 border border-slate-600 rounded p-2 text-white">
                        <button type="submit" class="bg-primary text-slate-900 font-bold py-2 rounded">Adicionar Key</button>
                    </div>
                </form>

                <div class="space-y-2">
                    @foreach($keys as $key)
                    <div class="flex justify-between items-center bg-slate-800 p-3 rounded border border-slate-700">
                        <div>
                            <span class="text-xs font-bold uppercase {{ $key->provider == 'gemini' ? 'text-blue-400' : 'text-green-400' }}">{{ $key->provider }}</span>
                            <p class="text-white font-mono text-sm">•••• {{ substr($key->api_key, -5) }}</p>
                            <p class="text-[10px] text-slate-500">Erros: {{ $key->error_count }} | Uso: {{ $key->last_used_at ?? 'Nunca' }}</p>
                        </div>
                        <form action="{{ route('admin.delete_key', $key->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-400"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-card border border-slate-700 p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-white font-bold">Taxas de Mercado (Crawler IA)</h3>
                    <form action="{{ route('admin.force_update') }}" method="POST">
                        @csrf
                        <button class="text-xs bg-purple-600 hover:bg-purple-500 text-white px-3 py-1 rounded flex items-center gap-2">
                            <i class="fa-solid fa-robot"></i> Forçar Atualização Agora
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-slate-500 border-b border-slate-700 text-xs uppercase">
                            <tr>
                                <th class="pb-2">Plataforma</th>
                                <th class="pb-2">Tipo</th>
                                <th class="pb-2">Comissão</th>
                                <th class="pb-2">Fixo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($rates as $rate)
                            <tr>
                                <td class="py-3 text-white capitalize">{{ $rate->platform }}</td>
                                <td class="py-3 text-slate-400 capitalize">{{ $rate->listing_type }}</td>
                                <td class="py-3 text-emerald-400 font-bold">{{ $rate->commission_percent }}%</td>
                                <td class="py-3 text-slate-300">R$ {{ $rate->fixed_fee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-[10px] text-slate-500 mt-4 text-center">
                    Última verificação: {{ $rates->first()->last_check_at ?? '-' }} via {{ $rates->first()->updated_via ?? '-' }}
                </p>
            </div>

        </div>
    </div>
</body>
</html>