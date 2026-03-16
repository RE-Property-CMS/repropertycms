<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Setup Wizard') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- =====================================================================
         TOP PROGRESS BAR
         ===================================================================== --}}
    @php
        $currentStep = (int) ($currentStep ?? 0);
        $steps = [
            0 => 'Verify Key',
            1 => 'Requirements',
            2 => 'Database',
            3 => 'Admin',
            4 => 'Mail',
            5 => 'Payments',
            6 => 'Storage',
            7 => 'Security',
            8 => 'Complete',
        ];
    @endphp

    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <div class="flex-shrink-0 mr-8">
                    <span class="text-lg font-bold text-brand-600">
                        {{ config('app.name', 'RePropertyCMS') }}
                    </span>
                    <span class="text-xs text-gray-400 block leading-none">Setup Wizard</span>
                </div>

                {{-- Steps --}}
                <div class="flex items-center flex-1 overflow-x-auto">
                    @foreach($steps as $num => $label)
                        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                            {{-- Step circle --}}
                            <div class="flex flex-col items-center flex-shrink-0">
                                @if($num < $currentStep)
                                    {{-- Completed --}}
                                    <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @elseif($num === $currentStep)
                                    {{-- Active --}}
                                    <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center ring-4 ring-brand-100">
                                        <span class="text-white text-xs font-bold">{{ $num }}</span>
                                    </div>
                                @else
                                    {{-- Upcoming --}}
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <span class="text-gray-400 text-xs font-medium">{{ $num }}</span>
                                    </div>
                                @endif
                                <span class="text-xs mt-1 font-medium whitespace-nowrap
                                    {{ $num === $currentStep ? 'text-brand-600' : ($num < $currentStep ? 'text-brand-400' : 'text-gray-400') }}">
                                    {{ $label }}
                                </span>
                            </div>

                            {{-- Connector line --}}
                            @if(!$loop->last)
                                <div class="flex-1 h-0.5 mx-2 {{ $num < $currentStep ? 'bg-brand-600' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Step counter --}}
                <div class="flex-shrink-0 ml-8 text-right">
                    <span class="text-xs text-gray-400">Step</span>
                    <span class="text-sm font-bold text-brand-600">{{ $currentStep + 1 }}</span>
                    <span class="text-xs text-gray-400">of {{ count($steps) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         MAIN SPLIT PANEL
         ===================================================================== --}}
    <div class="flex flex-1 min-h-0" style="min-height: calc(100vh - 88px)">

        {{-- Left: Form Panel (60%) --}}
        <div class="w-3/5 bg-white flex flex-col justify-center px-12 py-10 overflow-y-auto">
            <div class="max-w-lg w-full mx-auto">
                @yield('content')
            </div>
        </div>

        {{-- Right: Illustration Panel (40%) --}}
        <div class="w-2/5 bg-gradient-to-br from-brand-600 to-brand-800 flex flex-col items-center justify-center px-10 py-12 text-white">
            <div class="max-w-xs w-full text-center">
                {{-- Step-specific illustration --}}
                @yield('illustration')

                <h3 class="text-xl font-semibold mt-6 leading-snug">
                    @yield('illustration_title', 'Setting up your platform')
                </h3>
                <p class="text-brand-200 text-sm mt-2 leading-relaxed">
                    @yield('illustration_text', 'Follow these simple steps to complete your installation.')
                </p>
            </div>
        </div>

    </div>

</body>
</html>
