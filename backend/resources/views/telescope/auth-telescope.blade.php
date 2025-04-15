<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dev Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<form method="POST" action="/dev-login" class="bg-white p-6 rounded-xl shadow-md w-80 space-y-4">
    @csrf
    <h1 class="text-xl font-bold text-center">Dev Login</h1>

    <label class="block">
        <span class="text-gray-700">Email</span>
        <input type="email" name="email" required
               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring focus:ring-indigo-200">
    </label>

    <button type="submit"
            class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
        Login
    </button>
</form>
</body>
</html>
