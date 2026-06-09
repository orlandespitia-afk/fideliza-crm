<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fideliza CRM - Ingreso</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-blue-600 tracking-tight">Fideliza CRM 🗳️</h1>
            <p class="text-gray-500 mt-2 text-sm">Control Político y Gestión de Votantes</p>
        </div>

        @if($errors->has('username'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-xl mb-6 text-sm">
            <p class="font-bold">Error de ingreso:</p>
            <p>{{ $errors->first('username') }}</p>
        </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Nombre de Usuario</label>
                <input type="text" name="username" required autofocus placeholder="ej. ramiro"
                    class="w-full border-2 border-gray-200 p-4 rounded-xl focus:border-blue-500 outline-none text-lg">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Contraseña</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full border-2 border-gray-200 p-4 rounded-xl focus:border-blue-500 outline-none text-lg">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold p-4 rounded-xl shadow-lg hover:bg-blue-700 text-lg">
                Ingresar al Sistema
            </button>
        </form>
    </div>

</body>

</html>