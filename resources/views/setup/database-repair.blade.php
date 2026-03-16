<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Repair</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center max-w-lg px-6">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-gray-800 mb-3">Database Configuration Issue</h1>
        <p class="text-gray-500 text-sm leading-relaxed mb-6">
            The application is marked as installed but cannot connect to the database, or the installation record is missing. This usually happens if the database was reset or the <code class="bg-gray-100 px-1 rounded">.env</code> file was modified.
        </p>

        <div class="p-5 bg-white border border-gray-200 rounded-xl text-left mb-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Possible Causes</p>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    Database credentials in <code class="bg-gray-100 px-1 rounded text-xs">.env</code> are incorrect
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    The database server is not running or unreachable
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    Migrations were not run or the database was dropped
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    <code class="bg-gray-100 px-1 rounded text-xs">integration_settings</code> table is missing
                </li>
            </ul>
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('setup.database') }}"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                Re-configure Database
            </a>
            <a href="{{ route('setup.index') }}"
                class="w-full border border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-800 font-medium py-3 rounded-lg transition-colors duration-200">
                Return to Setup Wizard
            </a>
        </div>
    </div>
</body>
</html>
