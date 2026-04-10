<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Complete — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white flex items-center justify-center px-6">

    <div class="max-w-xl w-full text-center">
        <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa fa-check-circle text-green-400 text-4xl"></i>
        </div>

        <h1 class="text-3xl font-bold mb-3">You've seen the full system</h1>
        <p class="text-gray-300 text-lg mb-8 leading-relaxed">
            You've experienced the complete flow — from admin setup through agent listing management to the buyer-facing property page.
        </p>

        <div class="bg-white/10 border border-white/20 rounded-2xl p-6 mb-8 text-left space-y-3">
            <div class="flex items-center gap-3 text-sm">
                <i class="fa fa-check text-green-400 w-4"></i>
                <span class="text-gray-200">Admin panel — agents, plans, subscriptions, settings</span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fa fa-check text-green-400 w-4"></i>
                <span class="text-gray-200">Agent portal — property listings, media, billing</span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fa fa-check text-green-400 w-4"></i>
                <span class="text-gray-200">Buyer view — public property page with gallery and inquiry</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('demo.landing') }}"
               class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold px-8 py-4 rounded-xl transition-all">
                <i class="fa fa-rotate-left"></i>
                Start Again
            </a>
        </div>

        <p class="text-gray-500 text-sm mt-8">
            Your demo data has been queued for automatic cleanup.
        </p>
    </div>

</body>
</html>
