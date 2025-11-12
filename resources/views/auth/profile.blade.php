@extends('layouts.app')
@section('title', 'Meu Perfil')

@push('scripts')
<script>
    // Marcar página como profile para esconder seletor de tema
    document.addEventListener('DOMContentLoaded', function() {
        document.body.setAttribute('data-route', 'profile');
        // Esconder seletor de tema do canto
        const themeSelector = document.getElementById('themeSelector');
        if (themeSelector) {
            themeSelector.style.display = 'none';
        }
    });
</script>
@endpush

@section('content')
    <div class="row">
        <!-- Coluna Esquerda - Dados do Perfil -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background: var(--theme-gradient-primary);">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-user me-2"></i>Dados do Perfil
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Nome
                            </label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nome completo" 
                                   value="{{ old('name', auth()->user()->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>E-mail
                            </label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="seu@email.com" 
                                   value="{{ old('email', auth()->user()->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Nova Senha
                            </label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Deixe em branco para manter a senha atual"
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Deixe em branco se não deseja alterar a senha
                            </small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2"></i>Confirmar Nova Senha
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control"
                                   placeholder="Confirme a nova senha"
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna Direita - Configurações de Aparência -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background: var(--theme-gradient-primary);">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-palette me-2"></i>Configurações de Aparência
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Seletor de Tema -->
                    <div class="form-group mb-4">
                        <label class="form-label d-block mb-3">
                            <i class="fas fa-paint-brush me-2"></i>Tema de Cores
                        </label>
                        <div class="d-flex gap-4 justify-content-center" id="themeSelectorContainer">
                            <div class="theme-option" data-theme="yellow">
                                <div class="theme-preview theme-yellow"></div>
                                <span class="theme-name">Amarelo Primário</span>
                            </div>
                            <div class="theme-option" data-theme="blue">
                                <div class="theme-preview theme-blue"></div>
                                <span class="theme-name">Azul Escuro</span>
                            </div>
                        </div>
                    </div>

                    <!-- Seletor de Cor de Fundo -->
                    <div class="form-group mb-4">
                        <label class="form-label d-block mb-3">
                            <i class="fas fa-fill me-2"></i>Cor de Fundo
                        </label>
                        <div class="d-flex gap-4 justify-content-center" id="backgroundSelectorContainer">
                            <div class="background-option" data-background="white">
                                <div class="background-preview bg-white"></div>
                                <span class="background-name">Branco</span>
                            </div>
                            <div class="background-option" data-background="black">
                                <div class="background-preview bg-black"></div>
                                <span class="background-name">Preto</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        As alterações são salvas automaticamente no seu navegador.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .theme-option, .background-option {
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        padding: 20px 25px;
        border-radius: 10px;
        border: 3px solid transparent;
        background: var(--theme-gray-light);
        margin: 0 8px;
    }

    .theme-option:hover, .background-option:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .theme-option.active, .background-option.active {
        border-color: var(--theme-primary);
        box-shadow: 0 0 0 4px rgba(182, 169, 9, 0.2);
    }

    .theme-preview {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        margin: 0 auto 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .theme-yellow {
        background: linear-gradient(135deg, #B6A909 0%, #D4C71C 100%);
    }

    .theme-blue {
        background: linear-gradient(135deg, #023D78 0%, #034A94 100%);
    }

    .background-preview {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        margin: 0 auto 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 2px solid var(--theme-border);
        transition: all 0.3s ease;
    }

    .bg-black {
        background-color: #1A1A1A;
    }

    .bg-white {
        background-color: #FFFFFF;
    }

    .theme-name, .background-name {
        display: block;
        font-weight: bold;
        font-size: 14px;
        color: var(--theme-text);
        margin-top: 8px;
    }

    .form-label {
        font-weight: bold;
        color: var(--theme-text);
        font-size: 15px;
    }

    .form-control {
        height: 42px;
        border-radius: 8px;
        border: 2px solid var(--theme-border);
        padding: 10px 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--theme-primary);
        box-shadow: 0 0 0 4px rgba(182, 169, 9, 0.15);
        outline: none;
    }

    [data-background="black"] body,
    [data-background="black"] .content-wrapper {
        background-color: #1A1A1A !important;
    }

    [data-background="black"] .card {
        background-color: #2A2A2A !important;
        color: #FFFFFF !important;
    }

    [data-background="black"] .form-control {
        background-color: #3A3A3A !important;
        color: #FFFFFF !important;
        border-color: #4A4A4A !important;
    }

    [data-background="black"] .form-label {
        color: #FFFFFF !important;
    }

    /* Espaçamento dos ícones na página de perfil */
    .form-label i,
    .card-title i,
    .alert i,
    .btn i {
        margin-right: 8px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obter preferências salvas
        const savedTheme = localStorage.getItem('nix-theme') || 'yellow';
        const savedBackground = localStorage.getItem('nix-background') || 'white';

        // Aplicar tema salvo
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-background', savedBackground);

        // Atualizar seletores visuais
        updateThemeSelector(savedTheme);
        updateBackgroundSelector(savedBackground);

        // Event listeners para tema
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                applyTheme(theme);
            });
        });

        // Event listeners para cor de fundo
        document.querySelectorAll('.background-option').forEach(option => {
            option.addEventListener('click', function() {
                const background = this.getAttribute('data-background');
                applyBackground(background);
            });
        });

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('nix-theme', theme);
            updateThemeSelector(theme);
            
            // Disparar evento para outros scripts
            const event = new CustomEvent('themeChanged', { detail: { theme: theme } });
            document.dispatchEvent(event);
        }

        function applyBackground(background) {
            document.documentElement.setAttribute('data-background', background);
            localStorage.setItem('nix-background', background);
            updateBackgroundSelector(background);
        }

        function updateThemeSelector(activeTheme) {
            document.querySelectorAll('.theme-option').forEach(option => {
                if (option.getAttribute('data-theme') === activeTheme) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        }

        function updateBackgroundSelector(activeBackground) {
            document.querySelectorAll('.background-option').forEach(option => {
                if (option.getAttribute('data-background') === activeBackground) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        }
    });
</script>
@endpush
