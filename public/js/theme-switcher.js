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

    // Criar seletor de tema - DESABILITADO (agora está no perfil)
    function createThemeSelector() {
        // Seletor de tema foi movido para a página de perfil
        // Apenas inicializar com tema salvo do localStorage
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



    // Exportar função para uso externo
    window.NixTheme = {
        setTheme: applyTheme,
        getTheme: getSavedTheme,
        themes: themes
    };

})();

