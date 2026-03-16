<aside class="md:block md:w-auto" aria-label="Sidebar">
  <ul>
    <li>
      <a href="{{url('agent/dashboard')}}" class="py-2">
          <img src="{{ url('images/logo_small.png') }}" alt="">
      </a>
    </li>
      <li>
          <a href="{{url('backend/dashboard')}}"><i class="fas fa-th-large pr-2"></i>
              <span>Dashboard</span>
          </a>
      </li>
      <li>
          <a href="{{url('backend/agent-listing')}}"><i class="fas fa-user pr-2"></i>
              <span>Agents</span>
          </a>
      </li>
      <li>
          <a href="{{url('backend/all-properties')}}"><i class="fas fa-home pr-2"></i>
              <span>All Properties</span>
          </a>
      </li>
      <li>
          <a href="{{url('backend/plans/index')}}"><i class="fa-solid fa-square-check pr-2"></i>
              <span>Plans</span>
          </a>
      </li>
      <li>
          <a href="{{route('backend.subscriber.index')}}"><i class="fa-solid fa-dollar-sign pr-2"></i>
              <span>Subscriber</span>
          </a>
      </li>
      <li>
          <a href="{{route('backend.pages.lists')}}"><i class="fas fa-home pr-2"></i>
              <span>Pages</span>
          </a>
      </li>
      <li>
          <a href="{{route('backend.settings.index')}}"><i class="fas fa-cog pr-2"></i>
              <span>Settings</span>
          </a>
      </li>
      <li>
          <a href="{{url('backend/sign-out')}}">
        <i class="fas fa-door-open pr-2"></i>
        <span>Sign Out</span>
      </a>
    </li>
  </ul>
</aside>