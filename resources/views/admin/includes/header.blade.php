@php
    $brand = cache()->remember('brand_settings', 3600, fn() =>
        \Illuminate\Support\Facades\DB::table('brand_settings')->first()
    );
    $brandLogo = ($brand && $brand->logo_path) ? asset($brand->logo_path) : asset('images/logo-placeholder-small.png');
@endphp
<aside class="md:block md:w-auto" aria-label="Sidebar">
  <ul>
    <li>
      <a href="{{url('agent/dashboard')}}" class="py-2">
          <img src="{{ $brandLogo }}" alt="{{ config('app.name') }}">
      </a>
    </li>
      <li>
          <a href="{{url('admin/dashboard')}}"><i class="fas fa-th-large pr-2"></i>
              <span>Dashboard</span>
          </a>
      </li>
      @if(!auth('admin')->user()?->is_super_admin)
      <li>
          <a href="{{url('admin/agent-listing')}}"><i class="fas fa-user pr-2"></i>
              <span>Agents</span>
          </a>
      </li>
      <li>
          <a href="{{url('admin/all-properties')}}"><i class="fas fa-home pr-2"></i>
              <span>All Properties</span>
          </a>
      </li>
      <li>
          <a href="{{url('admin/plans/index')}}"><i class="fa-solid fa-square-check pr-2"></i>
              <span>Plans</span>
          </a>
      </li>
      <li>
          <a href="{{route('admin.subscriber.index')}}"><i class="fa-solid fa-dollar-sign pr-2"></i>
              <span>Subscriber</span>
          </a>
      </li>
      @endif
      @if(auth('admin')->user()?->is_super_admin)
      <li>
          <a href="{{route('admin.demo.sessions')}}"><i class="fas fa-flask pr-2"></i>
              <span>Demo Sessions</span>
          </a>
      </li>
      <li>
          <a href="{{route('admin.pages.lists')}}"><i class="fas fa-paint-brush pr-2"></i>
              <span>Page Builder</span>
          </a>
      </li>
      @if(env('LICENSE_OWNER') === 'true')
      <li>
          <a href="{{route('admin.licenses.dashboard')}}"><i class="fas fa-key pr-2"></i>
              <span>Licenses</span>
          </a>
      </li>
      @endif
      @endif
      @if(session('demo_session_id'))
      <li>
          <a href="{{ route('demo.wizard.requirements') }}"><i class="fas fa-magic pr-2"></i>
              <span>Replay Setup Wizard</span>
          </a>
      </li>
      @endif
      <li>
          <a href="{{route('admin.settings.index')}}"><i class="fas fa-cog pr-2"></i>
              <span>Settings</span>
          </a>
      </li>
      <li>
          <a href="{{route('admin.settings.docs')}}"><i class="fas fa-book pr-2"></i>
              <span>Help & Docs</span>
          </a>
      </li>
      <li>
          <a href="{{url('admin/sign-out')}}">
        <i class="fas fa-door-open pr-2"></i>
        <span>Sign Out</span>
      </a>
    </li>
  </ul>
</aside>
