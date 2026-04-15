<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center">
    <div class="max-w-md w-full mx-auto p-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-green-600 to-teal-600 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-key text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800">Nueva contraseña</h2>
                <p class="text-gray-500 mt-2">Ingresa tu nueva contraseña</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Correo electrónico</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Nueva contraseña</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Mínimo 8 caracteres">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Repite tu contraseña">
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white font-bold py-3 px-4 rounded-lg hover:from-green-700 hover:to-teal-700 transition shadow-lg">
                    Restablecer contraseña
                </button>
            </form>
        </div>
    </div>
</body>
</html>