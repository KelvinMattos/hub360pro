<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PrismaHUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md border border-gray-700">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">PrismaHUB</h1>
            <p class="text-gray-400 text-sm">Acesse sua conta</p>
        </div>
        
        <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-gray-300 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" class="w-full p-3 rounded bg-gray-900 text-white border border-gray-600 focus:border-blue-500 outline-none" required value="{{ old('email') }}">
            </div>

            <div>
                <label class="block text-gray-300 text-sm font-bold mb-2">Senha</label>
                <input type="password" name="password" class="w-full p-3 rounded bg-gray-900 text-white border border-gray-600 focus:border-blue-500 outline-none" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded transition">Entrar</button>
        </form>

        @if($errors->any())
            <div class="mt-4 p-3 bg-red-500/20 text-red-400 text-center rounded text-sm border border-red-500/50">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</body>
</html>