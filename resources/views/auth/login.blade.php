@extends('layouts.guest')

@section('content')
    <div class="card-body login-card-body">
       <div>
       <p class="login-box-msg">Bem-vindo de volta</p>
        <p class="login-subtitle">Faça login para acessar o sistema</p>

        <form action="{{ route('login') }}" method="post" id="loginForm">
            @csrf

            <div class="input-group mb-3">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ __('Email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback d-block">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="input-group mb-3">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Senha" required>
                @error('password')
                    <span class="invalid-feedback d-block">
                        {{ $message }}
                    </span>
                @enderror
            </div>

                    <button type="submit" class="btn btn-primary btn-block" id="loginButton">
                        <span class="login-button-text">Entrar</span>
                        <span class="login-button-loading d-none">
                            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                            Entrando...
                        </span>
                    </button>
        </form>
       </div>

        @if (Route::has('password.request'))
            <p class="mb-0 mt-3 text-center">
                <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
            </p>
        @endif
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const text = button.querySelector('.login-button-text');
            const loading = button.querySelector('.login-button-loading');
            
            text.classList.add('d-none');
            loading.classList.remove('d-none');
            button.disabled = true;
        });
    </script>
@endsection
