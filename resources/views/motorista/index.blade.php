@extends('layouts.app')
@section('title', 'motoristas')

@section('actions')
  <a href="{{ route('motorista.create') }}" class="btn btn-primary">
    Cadastrar motorista
  </a>
@endsection


@section('content')

  <form class="mr-2 d-flex justify-content-between" id="formSearch" action="{{ route('motorista.index') }}" method="GET">
    <div class="d-flex ">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
        </div>
        <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" class="form-control"
          placeholder="" aria-label="" aria-describedby="basic-addon1">
        <a href="{{ route('motorista.index') }}" class="btn btn-primary">Limpar busca</a>

      </div>
    </div>
    <div class="d-flex">
      <div class="input-group  ">
        <select id="paginacao" name="paginacao" class="custom-select mr-2" style="min-width: 80px"
          id="inputGroupSelect01">
          <option value="10" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '10' ? 'selected' : '' }}>
            10
          </option>
          <option value="20" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '20' ? 'selected' : '' }}>
            20
          </option>
          <option value="30" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '30' ? 'selected' : '' }}>
            30
          </option>


        </select>
        {{ $motoristas->appends([
            'paginacao' => $_GET['paginacao'] ?? 10,
            'search' => $_GET['search'] ?? '',
        ]) }}

      </div>
    </div>
  </form>
  @if (!$motoristas->isEmpty())
    <table id="motoristaTable" class="table shadow rounded table-striped table-hover">
      <thead class="bg-primary ">
        <tr>
          <th>Id</th>
          <th>Nome</th>
          <th>Turno</th>
          <th class="d-flex justify-content-center">Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($motoristas as $motorista)
          <tr @if ($motorista->deleted_at != null) style="background-color:#ff8e8e" @endif>
            <td>{{ $motorista->id }}</td>
            <td>{{ $motorista->nome }}</td>
            <td>{{ $motorista->turno }}</td>
            <td class="d-flex  justify-content-around">
              @if ($motorista->deleted_at == null)
                <a href="{{ route('motorista.edit', $motorista->id) }}" type="button"
                  class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
              @endif
              <form method="POST"
                action="{{ route($motorista->deleted_at == null ? 'motorista.destroy' : 'motorista.ativar', $motorista->id) }}"
                enctype="multipart/form-data">
                @if ($motorista->deleted_at == null)
                  @method('DELETE')
                @else
                  @method('PUT')
                @endif
                @csrf
                <button type="submit" class="btn {{ $motorista->deleted_at == null ? 'btn-danger' : 'btn-success' }}"><i
                    class="fa fa-power-off"></i></button>

              </form>



            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <x-not-found />
  @endif

  <!-- Modal -->



@endsection

@push('scripts')
  <script>
    document.getElementById('search').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })

    document.getElementById('paginacao').addEventListener('change', function() {
      document.getElementById('formSearch').submit()
    })
  </script>
@endpush