@auth
    @if(auth()->user()->role === 'admin')
        @include('layouts.navigation')
    @else
        @include('layouts.nav-user')
    @endif
@endauth