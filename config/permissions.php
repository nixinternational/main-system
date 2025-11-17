<?php

return [
    'cliente' => [
        'index' => 'clientes_listar',
        'create' => 'clientes_cadastrar',
        'store' => 'clientes_cadastrar',
        'edit' => 'clientes_editar',
        'update' => 'clientes_dados_cadastrais_atualizar',
        'show' => 'clientes_dados_cadastrais_visualizar',
        'destroy' => 'clientes_inativar',
    ],
    'cliente_extras' => [
        'updateClientEmail' => 'clientes_responsaveis_gerenciar',
        'updateClientResponsaveis' => 'clientes_responsaveis_gerenciar',
        'updateClientAduanas' => 'clientes_aduanas_gerenciar',
        'updateClientEspecificidades' => 'clientes_siscomex_atualizar',
        'updateClientDocument' => 'clientes_documentos_upload',
        'destroyBancoCliente' => 'clientes_fornecedores_inativar',
        'deleteDocument' => 'clientes_documentos_excluir',
    ],
    'catalogo' => [
        'index' => 'catalogos_listar',
        'create' => 'catalogos_cadastrar',
        'store' => 'catalogos_cadastrar',
        'edit' => 'catalogos_editar',
        'update' => 'catalogos_produtos_atualizar',
        'show' => 'catalogos_dados_visualizar',
        'destroy' => 'catalogos_excluir',
    ],
    'processo' => [
        'index' => 'processos_listar',
        'create' => 'processos_criar',
        'store' => 'processos_criar',
        'edit' => 'processos_editar',
        'update' => 'processos_dados_atualizar',
        'show' => 'processos_dados_visualizar',
        'destroy' => 'processos_excluir',
    ],
    'processo_extras' => [
        'processoCliente' => 'processos_listar',
        'esbocoPdf' => 'processos_esboco_visualizar',
        'updateProcesso' => 'processos_produtos_gerenciar',
        'camposCabecalho' => 'processos_dados_atualizar',
        'destroyProduto' => 'processos_produtos_excluir',
        'updatecurrencies' => 'processos_cotacoes_atualizar',
    ],
    'produto' => [
        'store' => 'catalogos_produtos_salvar',
        'update' => 'catalogos_produtos_atualizar',
        'destroy' => 'catalogos_produtos_excluir',
    ],
    'processo_produto' => [
        'batchDelete' => 'processos_produtos_excluir',
    ],
];

