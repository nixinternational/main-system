<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Nix International') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- ✅ load jQuery ✅ -->
    <script src="{{ asset('js/vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('js/vendor/select2.min.js') }}"></script>
    <script src="{{ asset('js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('js/vendor/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script src="{{ asset('js/vendor/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('js/vendor/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap-datepicker.br.min.js') }}"></script>
    <script src="{{ asset('js/vendor/jquery.datetimepicker.full.min.js') }}"></script>
    <link href="{{ asset('css/vendor/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor/jquery.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vendor/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- Themes CSS -->
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">

    <!-- Aplicar tema imediatamente para evitar flash -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('nix-theme') || 'yellow';
                var background = localStorage.getItem('nix-background') || 'white';
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.setAttribute('data-background', background);
            } catch(e) {
                document.documentElement.setAttribute('data-theme', 'yellow');
                document.documentElement.setAttribute('data-background', 'white');
            }
        })();
    </script>

    <link rel="shortcut icon" href="{{ asset('icone.png') }}">

    <link
        rel="stylesheet"href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">

    @stack('styles')

</head>

<body class="hold-transition sidebar-mini sidebar-collapse w-100">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-light navbar-white d-flex justify-content-between">
            <ul class="navbar-nav d-flex align-items-center">

                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
                @if (app()->environment('local'))
                    <h5 style="background-color: red; color: white;padding:10px; border-radius:8px;">AMBIENTE DE DESENVOLVIMENTO</h5>
                @endif
                @php
                    $ipProtectionEnabled = Cache::get('ip_protection_enabled', false);
                @endphp
                <div class="ip-protection-status" data-enabled="{{ $ipProtectionEnabled ? 'true' : 'false' }}">
                    <span class="ip-protection-indicator">
                        <i class="fas {{ $ipProtectionEnabled ? 'fa-shield-alt' : 'fa-shield' }}"></i>
                        <span class="ip-protection-text">
                            {{ $ipProtectionEnabled ? 'PROTEÇÃO DE IP HABILITADA' : 'PROTEÇÃO DE IP DESABILITADA' }}
                        </span>
                    </span>
                    <form action="{{ route('toogle-ip-protection') }}" method="POST" class="ip-protection-toggle-form">
                        @csrf
                        <button type="submit" class="ip-protection-toggle-btn" title="{{ $ipProtectionEnabled ? 'Desabilitar proteção de IP' : 'Habilitar proteção de IP' }}">
                            <i class="fas {{ $ipProtectionEnabled ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            <span class="ip-protection-btn-text">{{ $ipProtectionEnabled ? 'ATIVO' : 'INATIVO' }}</span>
                        </button>
                    </form>
                </div>

            </ul>
            <div class="">

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">

                    <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">
                        @php
                            $avatarUrl = Auth::user()->avatar 
                                ? Storage::disk('public')->url('avatars/' . Auth::user()->avatar)
                                : null;
                        @endphp
                        <img src="{{ $avatarUrl ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&size=32&background=B6A909&color=fff' }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle mr-2 user-avatar" 
                             id="navbarAvatar"
                             data-user-name="{{ urlencode(Auth::user()->name) }}"
                             data-has-avatar="{{ Auth::user()->avatar ? 'true' : 'false' }}"
                             data-avatar-filename="{{ Auth::user()->avatar ?? '' }}"
                             style="width: 32px; height: 32px; object-fit: cover; border: 2px solid var(--theme-primary);"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(this.getAttribute('data-user-name')) + '&size=32&background=B6A909&color=fff';">
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="left: inherit; right: 0px;">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="mr-2 fas fa-file"></i>
                            {{ __('Meu perfil') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="dropdown-item"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="mr-2 fas fa-sign-out-alt"></i>
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/home" class="brand-link">
                <img src="{{ asset('images/logo-preto.png') }}" alt="Nix International" class="brand-logo" style="width: 140%; max-width: 200px;">
                <span class="brand-text d-none text-center font-weight-light">Nix International</span>
            </a>

            @include('layouts.navigation')
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="container-fluid">
                @hasSection('title')

                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2 d-flex justify-content-between">
                                <div class="d-flex justify-content around align-items-center">
                                    <h1 class="mr-3">@yield('title')</h1>
                                    @hasSection('tooltip')
                                        @yield('tooltip')
                                    @endif
                                </div>
                                <div class="">
                                    @hasSection('actions')
                                        @yield('actions')
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="content mt-2">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                @yield('content')

                            </div>
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->
        <!-- Main Footer -->

    </div>

    <!-- ./wrapper -->

    @if (\Session::has('messages'))
        <input type="hidden" name="messages" id="messages" value="{{ json_encode(\Session::get('messages')) }}">
    @endif

    <script src="{{ asset('js/adminlte.min.js') }}" defer></script>
    <!-- Theme Switcher - Apenas se não estiver na página de profile -->
    @if (!request()->routeIs('profile.show'))
        <script src="{{ asset('js/theme-switcher.js') }}"></script>
    @endif

    @vite('resources/js/app.js')

    @stack('scripts')

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'colored-toast',
            },
            animation: true,
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3500,
            timerProgressBar: true,
        })

        if (document.getElementById('messages') != null) {
            const messages = JSON.parse(document.getElementById('messages').value);
            const titles = {
                'error': 'Erro!',
                'success': 'Sucesso!'
            }
            for (let item in messages) {
                for (let message of messages[item]) {
                    console.log(message)
                    Toast.fire({
                        icon: `${item}`,
                        html: `${message}`,
                        title: titles[item]
                    });
                }
            }
        }

        // Atualizar avatar da navbar com tema correto
        document.addEventListener('DOMContentLoaded', function() {
            const navbarAvatar = document.getElementById('navbarAvatar');
            if (navbarAvatar && navbarAvatar.getAttribute('data-has-avatar') === 'false') {
                const theme = localStorage.getItem('nix-theme') || 'yellow';
                const themeColor = theme === 'blue' ? '023D78' : 'B6A909';
                const userName = navbarAvatar.getAttribute('data-user-name');
                navbarAvatar.src = 'https://ui-avatars.com/api/?name=' + userName + '&size=32&background=' + themeColor + '&color=fff';
            }
            
            // Atualizar avatar quando tema mudar
            document.addEventListener('themeChanged', function(event) {
                if (navbarAvatar && navbarAvatar.getAttribute('data-has-avatar') === 'false') {
                    const themeColor = event.detail.theme === 'blue' ? '023D78' : 'B6A909';
                    const userName = navbarAvatar.getAttribute('data-user-name');
                    navbarAvatar.src = 'https://ui-avatars.com/api/?name=' + userName + '&size=32&background=' + themeColor + '&color=fff';
                }
            });
        });
    </script>

</body>

</html>
