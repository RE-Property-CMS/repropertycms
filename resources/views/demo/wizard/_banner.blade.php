<div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-blue-800">You're previewing the installation wizard</p>
        <p class="text-sm text-blue-700 mt-0.5">This is exactly what you'll see when deploying your own copy of the CMS on your server. Every field is pre-filled with sample data so you can explore freely. <strong>Nothing on this server is changed.</strong></p>
    </div>
    <a href="{{ route('demo.wizard.finish') }}"
        class="flex-shrink-0 text-xs font-semibold text-blue-600 hover:text-blue-800 border border-blue-300 hover:border-blue-500 rounded-lg px-3 py-1.5 transition-colors whitespace-nowrap">
        Skip Tour →
    </a>
</div>
