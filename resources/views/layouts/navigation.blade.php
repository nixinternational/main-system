<!-- Sidebar -->
<div class="sidebar">

  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-item">
        <a href="{{ route('home') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-house"></i>
          <p>
            {{ __('Página Principal') }}
          </p>
        </a>
      </li>
      @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('cliente.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-user"></i>
          <p>
            Clientes
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('produto.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-box"></i>
          <p>
            Produtos
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('categoria.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-tags"></i>
          <p>
            Categorias de Produto
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('pedido.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-shopping-cart"></i>
          <p>
            Pedidos
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('motorista.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-truck"></i>
          <p>
            Motoristas
          </p>
        </a>
      </li>
    @endhasGroup
    
    <li class="nav-item">
      <a href="{{ route('producao.index') }}" class="nav-link">
        <i class="nav-icon fa-solid fa-industry"></i>
        <p>
          Cadastro de Produção
        </p>
      </a>
    </li>
    
    <li class="nav-item">
      <a href="{{ route('producaoBaixa.index') }}" class="nav-link">
        <i class="nav-icon fa-solid fa-clipboard-check"></i>
        <p>
          Baixa de Produção
        </p>
      </a>
    </li>
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="{{ route('pedido.atualiza') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-arrow-down"></i>
          <p>
            Baixa de Pedidos
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador|motorista')
      <li class="nav-item">
        <a href="{{ route('motorista.entrega.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-truck-loading"></i>
          <p>
            Entrega de Pedidos
          </p>
        </a>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-chart-bar"></i>
          <p>
            Relatórios
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview" style="display: none;">
          <li class="nav-item">
            <a href="{{ route('motorista.relatorio.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-truck"></i>
              <p>
                Relatórios de Motorista
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('cliente.relatorio.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-user"></i>
              <p>
                Relatórios de Cliente
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('produto.relatorio.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-box"></i>
              <p>
                Relatórios de Produtos
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('producao.relatorio') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-industry"></i>
              <p>
                Relatórios de Produção
              </p>
            </a>
          </li>
        </ul>
      </li>
    @endhasGroup
    
    @hasGroup('admnistrador')
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-cogs"></i>
          <p>
            Configurações
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview" style="display: none;">
          <li class="nav-item">
            <a href="{{ route('grupo.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-users-cog"></i>
              <p>
                Grupos de Permissão
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('permissao.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-key"></i>
              <p>
                Permissões
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('user.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-user"></i>
              <p>
                Usuários
              </p>
            </a>
          </li>
        </ul>
      </li>
    @endhasGroup
    

    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
