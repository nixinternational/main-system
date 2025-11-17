<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;

class PermissoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissoes = [
            // Clientes - Listagem
            ['slug' => 'clientes_listar', 'nome' => 'Clientes - Listar'],
            ['slug' => 'clientes_buscar', 'nome' => 'Clientes - Buscar'],
            ['slug' => 'clientes_cadastrar', 'nome' => 'Clientes - Cadastrar'],
            ['slug' => 'clientes_editar', 'nome' => 'Clientes - Editar'],
            ['slug' => 'clientes_inativar', 'nome' => 'Clientes - Inativar'],
            ['slug' => 'clientes_reativar', 'nome' => 'Clientes - Reativar'],
            // Clientes - Abas
            ['slug' => 'clientes_dados_cadastrais_visualizar', 'nome' => 'Clientes - Dados cadastrais (ver)'],
            ['slug' => 'clientes_dados_cadastrais_atualizar', 'nome' => 'Clientes - Dados cadastrais (atualizar)'],
            ['slug' => 'clientes_siscomex_visualizar', 'nome' => 'Clientes - Siscomex (ver)'],
            ['slug' => 'clientes_siscomex_atualizar', 'nome' => 'Clientes - Siscomex (atualizar)'],
            ['slug' => 'clientes_responsaveis_visualizar', 'nome' => 'Clientes - Responsáveis (ver)'],
            ['slug' => 'clientes_responsaveis_gerenciar', 'nome' => 'Clientes - Responsáveis (gerenciar)'],
            ['slug' => 'clientes_aduanas_visualizar', 'nome' => 'Clientes - Aduanas (ver)'],
            ['slug' => 'clientes_aduanas_gerenciar', 'nome' => 'Clientes - Aduanas (gerenciar)'],
            ['slug' => 'clientes_documentos_visualizar', 'nome' => 'Clientes - Documentos (ver)'],
            ['slug' => 'clientes_documentos_upload', 'nome' => 'Clientes - Documentos (upload)'],
            ['slug' => 'clientes_documentos_download', 'nome' => 'Clientes - Documentos (download)'],
            ['slug' => 'clientes_documentos_excluir', 'nome' => 'Clientes - Documentos (excluir)'],
            ['slug' => 'clientes_fornecedores_visualizar', 'nome' => 'Clientes - Fornecedores (ver)'],
            ['slug' => 'clientes_fornecedores_gerenciar', 'nome' => 'Clientes - Fornecedores (gerenciar)'],
            ['slug' => 'clientes_fornecedores_inativar', 'nome' => 'Clientes - Fornecedores (inativar)'],
            ['slug' => 'clientes_fornecedores_reativar', 'nome' => 'Clientes - Fornecedores (reativar)'],
            // Processos - Listagem & Cadastro
            ['slug' => 'processos_listar', 'nome' => 'Processos - Listar'],
            ['slug' => 'processos_filtrar', 'nome' => 'Processos - Filtrar'],
            ['slug' => 'processos_criar', 'nome' => 'Processos - Criar'],
            ['slug' => 'processos_editar', 'nome' => 'Processos - Editar'],
            ['slug' => 'processos_excluir', 'nome' => 'Processos - Excluir'],
            ['slug' => 'processos_dados_visualizar', 'nome' => 'Processos - Dados (ver)'],
            ['slug' => 'processos_dados_atualizar', 'nome' => 'Processos - Dados (atualizar)'],
            ['slug' => 'processos_cotacoes_atualizar', 'nome' => 'Processos - Atualizar cotações'],
            ['slug' => 'processos_produtos_visualizar', 'nome' => 'Processos - Produtos (ver)'],
            ['slug' => 'processos_produtos_gerenciar', 'nome' => 'Processos - Produtos (gerenciar)'],
            ['slug' => 'processos_produtos_excluir', 'nome' => 'Processos - Produtos (excluir)'],
            ['slug' => 'processos_esboco_visualizar', 'nome' => 'Processos - Esboço de NF'],
            // Catálogo
            ['slug' => 'catalogos_listar', 'nome' => 'Catálogo - Listar'],
            ['slug' => 'catalogos_buscar', 'nome' => 'Catálogo - Buscar'],
            ['slug' => 'catalogos_cadastrar', 'nome' => 'Catálogo - Cadastrar'],
            ['slug' => 'catalogos_editar', 'nome' => 'Catálogo - Editar'],
            ['slug' => 'catalogos_excluir', 'nome' => 'Catálogo - Excluir'],
            ['slug' => 'catalogos_dados_visualizar', 'nome' => 'Catálogo - Dados (ver)'],
            ['slug' => 'catalogos_produtos_listar', 'nome' => 'Catálogo - Produtos (ver)'],
            ['slug' => 'catalogos_produtos_filtros', 'nome' => 'Catálogo - Produtos (filtros)'],
            ['slug' => 'catalogos_produtos_adicionar', 'nome' => 'Catálogo - Produtos (adicionar)'],
            ['slug' => 'catalogos_produtos_salvar', 'nome' => 'Catálogo - Produtos (salvar)'],
            ['slug' => 'catalogos_produtos_editar', 'nome' => 'Catálogo - Produtos (editar)'],
            ['slug' => 'catalogos_produtos_atualizar', 'nome' => 'Catálogo - Produtos (atualizar)'],
            ['slug' => 'catalogos_produtos_excluir', 'nome' => 'Catálogo - Produtos (excluir)'],
            // Perfis especiais / compatibilidade
            ['slug' => 'root', 'nome' => 'Super Usuário'],
            ['slug' => 'admin', 'nome' => 'Administrador'],
            ['slug' => 'producao', 'nome' => 'Produção'],
        ];

        foreach ($permissoes as $permissao) {
            Permissao::updateOrCreate(
                ['slug' => $permissao['slug']],
                ['nome' => $permissao['nome']]
            );
        }

        $rootPermission = Permissao::where('slug', 'root')->first();
        $rootUser = \App\Models\User::where('email', \App\Models\User::SUPER_ADMIN_EMAIL)->first();

        if ($rootPermission && $rootUser) {
            $rootUser->permissoes()->syncWithoutDetaching([$rootPermission->id]);
        }
    }
}

