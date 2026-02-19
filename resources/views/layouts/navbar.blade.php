@auth
    @if(auth()->user()->role === 'admin')
        @include('layouts.navigation')

    @elseif(auth()->user()->role === 'user')
        @include('layouts.nav-user')

    @elseif(auth()->user()->role === 'sales_superintendent')
        @include('layouts.nav-ss')
    @endif
@endauth
