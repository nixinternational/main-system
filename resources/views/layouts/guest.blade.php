<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Nix International') }} - Login</title>

    <!-- Google Font: Source Sans Pro (Fallback) -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- Themes CSS -->
    <link rel="stylesheet" href="{{ asset('css/themes.css') }}">
    <!-- Login CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    
    <!-- Não aplicar tema na tela de login - manter sempre amarelo -->
    <script>
        (function() {
            // Forçar tema amarelo na tela de login
            document.documentElement.setAttribute('data-theme', 'yellow');
        })();
    </script>
    
    <link rel="shortcut icon" href="{{ asset('icone.png') }}">
</head>
<body class="hold-transition login-page">
<div class="card">
    <div class="row">
        <div class="col-7 py-3 d-flex justify-content-center align-items-center">
            <img class="login-logo" src="{{ asset('images/logo-nix2.png') }}" alt="Nix International Logo">
        </div>
        <div class="col-5 py-3 d-flex align-items-center">
            @yield('content')
        </div>
    </div>
</div>
<!-- /.login-box -->

@vite('resources/js/app.js')
<!-- Bootstrap 4 -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/adminlte.min.js') }}" defer></script>
</body>
</html>
