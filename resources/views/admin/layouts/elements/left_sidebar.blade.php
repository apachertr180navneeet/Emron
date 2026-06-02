<aside class="sidebar d-none d-md-flex">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="brand-text">
            <h1>{{ config('app.name') }}</h1>
            <span>Inventory Suite</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{route('admin.dashboard')}}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar-initials">{{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 2)) }}</div>
            <div class="user-text">
                <h4>{{ Auth::user()->full_name ?? Auth::user()->name ?? 'Admin' }}</h4>
                <p>{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <a href="{{route('admin.logout')}}" class="logout-btn" title="Logout">
            <i class="bi bi-box-arrow-right fs-5"></i>
        </a>
    </div>
</aside>