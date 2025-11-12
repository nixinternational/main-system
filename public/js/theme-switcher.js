/**
 * Sistema de Troca de Temas - NIX International
 */

(function() {
    'use strict';

    // Temas disponíveis
    const themes = {
        yellow: {
            name: 'Amarelo Primário',
            class: 'yellow',
            primary: '#B6A909',
            primaryBright: '#D4C71C'
        },
        blue: {
            name: 'Azul Escuro',
            class: 'blue',
            primary: '#023D78',
            primaryBright: '#034A94'
        }
    };

    // Obter tema salvo ou usar padrão (amarelo)
    function getSavedTheme() {
        return localStorage.getItem('nix-theme') || 'yellow';
    }

    // Salvar tema
    function saveTheme(theme) {
        localStorage.setItem('nix-theme', theme);
    }

    // Aplicar tema
    function applyTheme(theme) {
        const html = document.documentElement;
        html.setAttribute('data-theme', theme);
        saveTheme(theme);
        
        // Atualizar seletor visual
        updateThemeSelector(theme);
        
        // Disparar evento customizado
        const event = new CustomEvent('themeChanged', { detail: { theme: theme } });
        document.dispatchEvent(event);
    }

    // Criar seletor de tema
    function createThemeSelector() {
        // Verificar se já existe
        if (document.getElementById('themeSelector')) {
            return;
        }
        
        const selector = document.createElement('div');
        selector.className = 'theme-selector';
        selector.id = 'themeSelector';
        
        // Botões de tema - sempre visíveis
        Object.keys(themes).forEach(themeKey => {
            const theme = themes[themeKey];
            const btn = document.createElement('div');
            btn.className = `theme-selector-btn theme-${theme.class}`;
            btn.title = theme.name;
            btn.setAttribute('data-theme', themeKey);
            
            btn.addEventListener('click', function() {
                applyTheme(themeKey);
            });
            
            selector.appendChild(btn);
        });
        
        document.body.appendChild(selector);
        
        // Inicializar com tema salvo do localStorage
        const savedTheme = getSavedTheme();
        applyTheme(savedTheme);
    }

    // Atualizar seletor visual
    function updateThemeSelector(activeTheme) {
        const buttons = document.querySelectorAll('.theme-selector-btn');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-theme') === activeTheme) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Inicializar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createThemeSelector);
    } else {
        createThemeSelector();
    }

    // Exportar função para uso externo
    window.NixTheme = {
        setTheme: applyTheme,
        getTheme: getSavedTheme,
        themes: themes
    };

})();

