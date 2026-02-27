<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do Sistema | PrismaHUB</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { dark: '#0F172A', card: '#1E293B', primary: '#3483FA' },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['Fira Code', 'monospace'] }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-dark text-slate-300 font-sans h-screen flex overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-card border-b border-slate-700 p-6 shadow-md flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-terminal text-primary"></i> Logs do Sistema
                </h1>
                <p class="text-xs text-slate-400 mt-1">Monitoramento de eventos e erros em tempo real (Últimas 200 linhas).</p>
            </div>
            <a href="{{ route('settings.integrations') }}" class="text-sm text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
            </a>
        </header>

        <div class="flex-1 overflow-hidden p-6">
            <div class="bg-[#0d1117] border border-slate-700 rounded-xl h-full overflow-hidden flex flex-col shadow-2xl">
                <div class="bg-slate-800 px-4 py-2 border-b border-slate-700 flex justify-between items-center">
                    <span class="text-xs font-mono text-slate-400">storage/logs/laravel.log</span>
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                </div>
                <div class="flex-1 overflow-y-auto p-4 font-mono text-xs leading-relaxed custom-scrollbar">
                    @forelse($logs as $log)
                        <div class="mb-1 border-b border-slate-800/50 pb-1 hover:bg-white/5 transition px-2 rounded">
                            @if(Str::contains($log, '.ERROR'))
                                <span class="text-red-500 font-bold">[ERROR]</span> <span class="text-red-300">{{ $log }}</span>
                            @elseif(Str::contains($log, '.WARNING'))
                                <span class="text-yellow-500 font-bold">[WARN]</span> <span class="text-yellow-300">{{ $log }}</span>
                            @elseif(Str::contains($log, '.INFO'))
                                <span class="text-blue-500 font-bold">[INFO]</span> <span class="text-blue-300">{{ $log }}</span>
                            @else
                                <span class="text-slate-400">{{ $log }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-slate-600 mt-20">Arquivo de log vazio ou não encontrado.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>