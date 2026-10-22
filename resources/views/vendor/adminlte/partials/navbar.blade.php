@php
    $navLeft = app('adminlte')->menu('navbar-left');
@endphp
<nav class="app-header {{ config('adminlte.classes_topnav', 'navbar-expand bg-body') }} navbar">
    <div class="{{ config('adminlte.classes_topnav_container', 'container-fluid') }}">
        {{-- Left side --}}
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="{{ __('Toggle sidebar') }}">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </a>
            </li>          

            @foreach ($navLeft as $item)
                <li class="nav-item d-none d-md-block">
                    <a href="{{ $item['href'] ?? '#' }}" class="nav-link">{{ $item['text'] ?? '' }}</a>
                </li>
            @endforeach
        </ul>

        {{-- Right side --}}
        <ul class="navbar-nav ms-auto">
            {{-- Search (opens the ⌘K command palette) — single unified pill --}}

            {{-- Messages dropdown --}}
            @include('adminlte::partials.navbar-messages')

            {{-- Notifications dropdown --}}
            @include('adminlte::partials.navbar-notifications')

            {{-- Fullscreen toggle --}}
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="{{ __('Toggle fullscreen') }}">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none" aria-hidden="true"></i>
                </a>
            </li>          

            {{-- Control sidebar toggle (partials/control-sidebar.blade.php) --}}
            @if (config('adminlte.control_sidebar', false))
                <li class="nav-item">
                    <a class="nav-link" href="#" role="button"
                       data-bs-toggle="offcanvas" data-bs-target="#adminlte-control-sidebar"
                       aria-controls="adminlte-control-sidebar"
                       aria-label="{{ __('Toggle settings panel') }}">
                        <i class="bi bi-gear-fill" aria-hidden="true"></i>
                    </a>
                </li>
            @endif

            {{-- User menu --}}
            @if (config('adminlte.usermenu_enabled', true))
                @include('adminlte::partials.usermenu')
            @endif
        </ul>
    </div>
</nav>

@include('adminlte::partials.command-palette')
