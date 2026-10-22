@php
    $user = auth()->user();
    $name = $user?->nombre_usuario ?? 'Invitado';
    
    $avatar = asset('vendor/adminlte/img/user2-160x160.jpg'); 
    
    $showImage = (bool) config('adminlte.usermenu_image');
    $profileUrl = config('adminlte.usermenu_profile_url');
@endphp

<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
        <img src="{{ $avatar }}" class="user-image rounded-circle shadow" alt="{{ $name }}" width="30" height="30">
        <span class="d-none d-md-inline">{{ $name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        
        {{-- Footer --}}
        <li class="user-footer">
            @if ($profileUrl)
                <a href="{{ url($profileUrl) }}" class="btn btn-outline-secondary">
                    {{ __('adminlte.profile') }}
                </a>
            @endif
            <a href="#" class="btn btn-outline-danger {{ $profileUrl ? 'float-end' : 'w-100' }}"
               onclick="event.preventDefault(); document.getElementById('adminlte-logout-form').submit();">
                Cerrar Sesión
            </a>
            <form id="adminlte-logout-form" action="{{ url('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</li>