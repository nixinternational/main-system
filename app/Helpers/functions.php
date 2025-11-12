<?php
function formatCnpjCpf($value)
{
    $CPF_LENGTH = 11;
    $cnpj_cpf = preg_replace("/\D/", '', $value);

    if (strlen($cnpj_cpf) === $CPF_LENGTH) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
    }

    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}


function validar_cnpj($cnpj)
{
	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
	
	// Valida tamanho
	if (strlen($cnpj) != 14)
		return false;

	// Verifica se todos os digitos são iguais
	if (preg_match('/(\d)\1{13}/', $cnpj))
		return false;	

	// Valida primeiro dígito verificador
	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
	{
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}

	$resto = $soma % 11;

	if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
		return false;

	// Valida segundo dígito verificador
	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
	{
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}

	$resto = $soma % 11;

	return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

if (!function_exists('sortable')) {
    /**
     * Gera um link ordenável para colunas de tabela
     * 
     * @param string $column Nome da coluna para ordenar
     * @param string $label Texto do cabeçalho
     * @param string $route Nome da rota (opcional)
     * @return string HTML do link ordenável
     */
    function sortable($column, $label, $route = null)
    {
        try {
            $route = $route ?? request()->route()->getName();
            $currentColumn = request()->get('sort', 'id');
            $currentDirection = request()->get('direction', 'asc');
            
            $direction = 'asc';
            $icon = '';
            
            if ($currentColumn === $column) {
                $direction = $currentDirection === 'asc' ? 'desc' : 'asc';
                $icon = $currentDirection === 'asc' 
                    ? '<i class="fas fa-sort-up ml-1"></i>' 
                    : '<i class="fas fa-sort-down ml-1"></i>';
            } else {
                $icon = '<i class="fas fa-sort ml-1" style="opacity: 0.5;"></i>';
            }
            
            $queryParams = request()->except(['sort', 'direction', 'page']);
            $queryParams['sort'] = $column;
            $queryParams['direction'] = $direction;
            
            // Obter parâmetros da rota atual
            $routeParams = request()->route() ? request()->route()->parameters() : [];
            
            // Construir URL
            $url = route($route, array_merge($routeParams, $queryParams));
            
            return '<a href="' . $url . '" class="text-white text-decoration-none" style="display: inline-block;">' . 
                   $label . ' ' . $icon . '</a>';
        } catch (\Exception $e) {
            // Fallback: retornar apenas o label se houver erro
            return '<span class="text-white">' . $label . '</span>';
        }
    }
}