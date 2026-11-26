<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login • PatSons Goa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-stone-900 text-stone-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-stone-800 border border-stone-700 rounded-3xl p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-400/10 text-amber-400 border border-amber-400/20 mb-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-amber-400">PatSons Admin Portal</h1>
            <p class="text-xs text-stone-400">Dev Can Fruit Products • Morjim, Goa</p>
        </div>

        @if($errors->any())
            <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs font-bold p-3.5 rounded-2xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4 text-xs font-bold">
            @csrf
            <div>
                <label class="block uppercase tracking-wider text-stone-400 mb-1.5">Admin Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-stone-900 border border-stone-700 rounded-2xl px-4 py-3 text-stone-200 focus:ring-2 focus:ring-amber-400 outline-none">
            </div>

            <div>
                <label class="block uppercase tracking-wider text-stone-400 mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-stone-900 border border-stone-700 rounded-2xl px-4 py-3 text-stone-200 focus:ring-2 focus:ring-amber-400 outline-none">
            </div>

            <div class="flex items-center justify-between text-[11px] text-stone-400 pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-stone-900 border-stone-700 text-amber-400">
                    <span>Remember session</span>
                </label>
            </div>

            <button type="submit"
                class="w-full bg-amber-400 hover:bg-amber-300 text-stone-950 font-black py-3.5 rounded-2xl text-xs shadow-lg transition active:scale-95">
                Sign In to Dashboard →
            </button>
        </form>
    </div>

</body>

</html>