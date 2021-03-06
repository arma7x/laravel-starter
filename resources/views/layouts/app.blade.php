<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('widgets.firebase-assets')

    <!-- Scripts -->
    <script src="{{ asset('js/qrcode.min.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div id="app" class="d-flex flex-column h-100" style="height:100vh!important;">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-5 fixed-top">
            <div class="container">
                <div class="d-flex flex-row">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <div id="loadingIndicator" class="spinner-border text-secondary d-none" role="status">
                      <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login') && firebase::user() === null)
                                <li id="loginNavItem" class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register') && firebase::user() === null)
                                <li id="registerNavItem" class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif

                            @if (firebase::user() === null)
                            <li id="firebaseLoginBtn">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#firebaseLoginModal">{{ __('Firebase Login') }}</a>
                            </li>
                            @else
                            <li id="firebaseLogoutBtn">
                                <a class="nav-link" href="#" onclick="event.preventDefault();">Hi {{ firebase::user()['name'] ?? (firebase::user()['displayName'] ?? '') }}</a>
                            </li>
                            <li id="firebaseLogoutBtn">
                                <a class="nav-link" href="#" onclick="event.preventDefault();logoutFirebase();">{{ __('Firebase Logout') }}</a>
                            </li>
                            @endif
                        @else
                            @if (Auth::User()->access_level === 0)
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() === 'admin.manage-user' ? 'active' : ''; }}" href="{{ route('admin.manage-user') }}" onclick="{{ Route::current()->getName() === 'admin.manage-user' ? 'event.preventDefault();' : '' }}">{{ __('Manage User') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() === 'admin.manage-firebase-user' ? 'active' : ''; }}" href="{{ route('admin.manage-firebase-user') }}" onclick="{{ Route::current()->getName() === 'admin.manage-firebase-user' ? 'event.preventDefault();' : '' }}">{{ __('Manage Firebase User') }}</a>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::User()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item {{ Route::current()->getName() === 'manage-token' ? 'active' : '' }}" href="{{ route('manage-token') }}" onclick="{{ Route::current()->getName() === 'manage-token' ? 'event.preventDefault();' : '' }}">
                                        {{ __('Manage Token') }}
                                    </a>
                                    <a class="dropdown-item {{ Route::current()->getName() === 'ui-change-password' ? 'active' : '' }}" href="{{ route('change-password') }}" onclick="{{ Route::current()->getName() === 'ui-change-password' ? 'event.preventDefault();' : '' }}">
                                        {{ __('Change Password') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="flex-shrink-0" style="padding-top: 8em;">
            @foreach (Session::all() as $key => $value)
               @if (str_starts_with($key, 'has_'))
                <div class="alert alert-{{ str_replace('has_', '', $key) }} alert-dismissible text-center fade show" role="alert" style="margin-top: -3.7rem!important;">
                  <strong>{{ $value }}</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
               @endif
            @endforeach
            @yield('content')
        </main>
        @guest
            @include('widgets.firebase-login-modal')
        @endif
        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                <span class="text-muted">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}, {{ App::environment() }})</span>
            </div>
        </footer>
        @livewireScripts
    </div>
</body>
</html>
