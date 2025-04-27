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
      <li class="nav-item">
        <a href="{{ route('catalogo.index') }}" class="nav-link">
          <i class="nav-icon fa-solid fa-user"></i>
          <p>
            Catálogo
          </p>
        </a>
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
          <li class="nav-item">
            <a href="{{ route('banco-nix.index') }}" class="nav-link">
              <i class="nav-icon fa-solid fa-university"></i>
              <p>
                Bancos
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
