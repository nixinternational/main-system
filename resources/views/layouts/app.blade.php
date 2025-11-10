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
                @if (!Cache::get('ip_protection_enabled', false))
                    <h5 style="background-color: orange; color: black;padding:10px; border-radius:8px; margin-left: 10px;">
                        PROTEÇÃO DE IP DESABILITADA
                    </h5>
                @else
                    <h5 style="background-color: green; color: white; padding:10px; border-radius:8px; margin-left: 10px;">
                        PROTEÇÃO DE IP DESABILITADA
                    </h5>
                @endif

            </ul>
            <div class="">

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">

                    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa-solid fa-user mr-2"></i>
                        {{ Auth::user()->name }}
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
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color:#000">
            <!-- Brand Logo -->
            <a href="/home" class="brand-link  d-flex align-items-center flex-column">
                <img src="{{ asset('images/logo-preto.png') }}" alt="" class="" style=" width:140%">
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
    </script>

</body>

</html>
