@extends('layouts.app')
@section('actions')
@section('title', 'Usuários')

    <a href="{{ route('user.create') }}" class="btn btn-primary">
        Cadastrar usuario
    </a>
@endsection
@section('content')

    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body p-0">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>        <a  type="button" href="{{ route('user.edit', $user->id) }}"
                                            class="btn btn-warning mr-1 editModal"><i class="fas fa-edit"></i></a>
                           </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                       
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection