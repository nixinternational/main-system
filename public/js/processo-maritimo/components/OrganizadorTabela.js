/**
 * Componente para organizar e reordenar linhas da tabela
 */
export class OrganizadorTabela {
    /**
     * Adiciona separadores entre grupos de adição
     */
    adicionarSeparadoresAdicao() {
        const tbody = document.getElementById('productsBody');
        if (!tbody) return;

        const linhas = Array.from(tbody.querySelectorAll('tr:not(.separador-adicao)'));

        // Remover separadores existentes
        document.querySelectorAll('.separador-adicao').forEach(el => el.remove());

        if (linhas.length === 0) return;

        // Ordenar linhas por adição e item
        linhas.sort((a, b) => {
            if (!a || !b) return 0;
            
            const inputAdicaoA = a.querySelector('input[name*="[adicao]"]');
            const inputAdicaoB = b.querySelector('input[name*="[adicao]"]');
            const adicaoA = inputAdicaoA ? parseFloat(inputAdicaoA.value) || 0 : 0;
            const adicaoB = inputAdicaoB ? parseFloat(inputAdicaoB.value) || 0 : 0;

            if (adicaoA !== adicaoB) {
                return adicaoA - adicaoB;
            }

            const inputItemA = a.querySelector('input[name*="[item]"]');
            const inputItemB = b.querySelector('input[name*="[item]"]');
            const itemA = inputItemA ? parseFloat(inputItemA.value) || 0 : 0;
            const itemB = inputItemB ? parseFloat(inputItemB.value) || 0 : 0;
            return itemA - itemB;
        });

        // Agrupar por adição
        const grupos = {};
        linhas.forEach(linha => {
            if (!linha) return;
            
            const inputAdicao = linha.querySelector('input[name*="[adicao]"]');
            const adicao = inputAdicao ? parseFloat(inputAdicao.value) || 0 : 0;
            if (!grupos[adicao]) grupos[adicao] = [];
            grupos[adicao].push(linha);
        });

        // Limpar tbody
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        // Adicionar linhas com separadores
        Object.keys(grupos).sort((a, b) => a - b).forEach((adicao, index) => {
            if (index > 0) {
                const separador = document.createElement('tr');
                separador.className = 'separador-adicao';
                separador.innerHTML = `<td colspan="100" style="background-color: #000 !important; height: 2px; padding: 0;"></td>`;
                tbody.appendChild(separador);
            }

            grupos[adicao].forEach(linha => {
                tbody.appendChild(linha);
            });
        });
    }

    /**
     * Reordena linhas da tabela
     * @param {Function} triggerChange - Função para disparar eventos change
     */
    reordenarLinhas(triggerChange) {
        this.adicionarSeparadoresAdicao();

        // Disparar eventos change nos inputs e selects
        if (triggerChange) {
            triggerChange('#productsBody input:not([name*="[adicao]"])');
            triggerChange('#productsBody select');
        } else if (typeof $ !== 'undefined') {
            $('#productsBody input:not([name*="[adicao]"])').trigger('change');
            $('#productsBody select').trigger('change');
        }
    }
}
