{{--
  The demo access form — rendered as a Blade partial so it can be injected
  into both the standard Blade view AND into GrapesJS-built pages via
  DemoController's str_replace on <div data-demo="form"></div>.
--}}
<div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:32px 36px;width:100%;max-width:440px;margin:0 auto;">
    <h2 class="text-lg font-bold text-white mb-2 text-center">{{ sc('demo-landing','form_title','Get Your Private Demo') }}</h2>
    <p class="text-sm text-blue-300 text-center mb-6">{{ sc('demo-landing','form_subtitle','Credentials will be emailed to you instantly.') }}</p>

    <form method="POST" action="{{ route('demo.start') }}">
        @csrf

        {{-- Expired notice --}}
        @if($expired ?? false)
            <div class="mb-4 bg-amber-500/20 border border-amber-400/30 rounded-xl px-4 py-3 flex items-center gap-3">
                <i class="fa fa-clock text-amber-400"></i>
                <p class="text-amber-200 text-sm">Your demo session expired. Start a new one below.</p>
            </div>
        @endif

        {{-- Info notice --}}
        @if(session('info'))
            <div class="mb-4 bg-blue-500/20 border border-blue-400/30 rounded-xl px-4 py-3 flex items-center gap-3">
                <i class="fa fa-info-circle text-blue-400"></i>
                <p class="text-blue-200 text-sm">{{ session('info') }}</p>
            </div>
        @endif

        {{-- Name --}}
        <div class="mb-4">
            <label style="display:block;font-size:12px;font-weight:600;color:#94a3b8;margin-bottom:6px;letter-spacing:.04em;text-transform:uppercase;">
                Your Name <span style="color:#64748b;">(optional)</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Smith"
                   style="width:100%;padding:11px 14px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-radius:10px;color:#fff;font-size:14px;outline:none;"
                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='rgba(255,255,255,.15)'">
        </div>

        {{-- Email --}}
        <div class="mb-2">
            <label style="display:block;font-size:12px;font-weight:600;color:#94a3b8;margin-bottom:6px;letter-spacing:.04em;text-transform:uppercase;">
                Work Email <span style="color:#ef4444;">*</span>
            </label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@yourcompany.com" required
                   style="width:100%;padding:11px 14px;background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('email') ? '#ef4444' : 'rgba(255,255,255,.15)' }};border-radius:10px;color:#fff;font-size:14px;outline:none;"
                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='rgba(255,255,255,.15)'">
            @error('email')
                <p style="color:#f87171;font-size:12px;margin-top:5px;">
                    <i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <p style="font-size:11px;color:#475569;margin-bottom:18px;">
            <i class="fa fa-shield-halved" style="margin-right:4px;"></i>Temporary email services are not accepted.
        </p>

        <button type="submit"
                style="width:100%;padding:13px;background:#2563eb;color:#fff;font-size:15px;font-weight:700;border:none;border-radius:12px;cursor:pointer;transition:background .15s;"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
            <i class="fa fa-envelope mr-2"></i> Send My Demo Access
        </button>
    </form>

    <p class="text-gray-500 text-xs mt-5 text-center">
        <i class="fa fa-clock mr-1"></i> 60-minute session &nbsp;&bull;&nbsp;
        <i class="fa fa-trash mr-1"></i> Auto-deleted &nbsp;&bull;&nbsp;
        <i class="fa fa-lock mr-1"></i> Fully isolated
    </p>
</div>
