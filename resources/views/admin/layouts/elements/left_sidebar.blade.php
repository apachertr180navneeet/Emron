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
            @if(Auth::user()->role == 'admin')
            <li class="nav-item">
                <a href="{{route('admin.dashboard')}}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.company.index')}}" class="nav-link {{ request()->routeIs('admin.company.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Company Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.customer.index')}}" class="nav-link {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Customer Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.salesman.index')}}" class="nav-link {{ request()->routeIs('admin.salesman.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Salesman Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.unit.index')}}" class="nav-link {{ request()->routeIs('admin.unit.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers"></i> Unit Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item.index')}}" class="nav-link {{ request()->routeIs('admin.item.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Item Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item-assignment.index')}}" class="nav-link {{ request()->routeIs('admin.item-assignment.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Item Assignment
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.purchase.index')}}" class="nav-link {{ request()->routeIs('admin.purchase.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Purchase
                </a>
            </li>
            @else
            <li class="nav-item">
                <a href="{{route('company.dashboard')}}" class="nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.vendor.index')}}" class="nav-link {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}">
                    <i class="bi bi-buildings"></i> Vendor Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.customer.index')}}" class="nav-link {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Customer Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.salesman.index')}}" class="nav-link {{ request()->routeIs('admin.salesman.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Salesman Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.unit.index')}}" class="nav-link {{ request()->routeIs('admin.unit.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers"></i> Unit Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item.index')}}" class="nav-link {{ request()->routeIs('admin.item.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Item Master
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.item-assignment.index')}}" class="nav-link {{ request()->routeIs('admin.item-assignment.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i> Item Assignment
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.purchase.index')}}" class="nav-link {{ request()->routeIs('admin.purchase.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Purchase
                </a>
            </li>
            @endif
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
        <div class="d-flex gap-1">
            @if(Auth::user()->role == 'admin')
            <a href="{{route('admin.profile')}}" class="logout-btn" title="Profile"><i class="bi bi-person fs-5"></i></a>
            <a href="{{route('admin.logout')}}" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
            @else
            <a href="{{route('company.profile')}}" class="logout-btn" title="Profile"><i class="bi bi-person fs-5"></i></a>
            <a href="{{route('company.logout')}}" class="logout-btn" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
            @endif
        </div>
    </div>
</aside>