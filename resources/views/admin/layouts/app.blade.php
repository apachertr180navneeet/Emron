<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/img/favicon/favicon.ico')}}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('style')
</head>
<body>
    <div class="d-flex">
        @include('admin.layouts.elements.left_sidebar')
        <div class="main-wrapper">
            @include('admin.layouts.elements.header')
            <main class="main-content">
                @yield('content')
            </main>
            @include('admin.layouts.elements.footer')
        </div>
    </div>

    <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="sidebarOffcanvas" style="background:#0f172a!important">
        <div class="offcanvas-header border-bottom border-secondary">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon d-flex align-items-center justify-content-center rounded" style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.25)">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <h1 class="fs-6 fw-bold text-white mb-0">{{ config('app.name') }}</h1>
                    <span class="text-secondary" style="font-size:.625rem;text-transform:uppercase;letter-spacing:.1em">Inventory Suite</span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-3">
            <ul class="nav flex-column">
                <li class="nav-item mb-1">
                    <a href="{{route('admin.dashboard')}}" class="nav-link d-flex align-items-center rounded-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="color:#94a3b8;padding:.75rem 1rem;font-size:.875rem">
                        <i class="bi bi-grid-1x2 me-3 fs-5" style="color:#64748b"></i> Dashboard
                    </a>
                </li>
            </ul>
        </div>
        <div class="p-3 border-top border-secondary">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(99,102,241,.2);color:#a5b4fc;font-weight:700;font-size:.875rem">
                    {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 2)) }}
                </div>
                <div class="text-truncate">
                    <h6 class="text-white mb-0" style="font-size:.75rem">{{ Auth::user()->full_name ?? 'Admin' }}</h6>
                    <small class="text-secondary" style="font-size:.625rem">{{ Auth::user()->email ?? '' }}</small>
                </div>
            </div>
        </div>
    </div>

    @include('admin.layouts.elements.sweet_alerts')
    @yield('script')
</body>
</html>