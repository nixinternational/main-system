@extends('layouts.app')
@section('title', isset($cliente) ? "Fornecedores de $cliente->nome" : 'Cadastrar cliente')

@section('content')
    <div class="card">

        <div class="card-text p-3">
            <div class="w-100 d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#fornecedorModal">Adicionar
                    fornecedor</button>
            </div>
            @if (!$cliente->fornecedores->isEmpty())

                <table id="fornecedorTable" class="table shadow rounded table-striped table-hover">
                    <thead class="bg-primary ">
                        <tr>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>País</th>
                            <th class="d-flex justify-content-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cliente->fornecedores as $fornecedor)
                            <tr @if ($fornecedor->deleted_at != null) style="background-color:#ff8e8e" @endif>
                                <td>{{ $fornecedor->nome }}</td>
                                <td>{{ substr($fornecedor->cnpj, 0, 2) . '.' . substr($fornecedor->cnpj, 2, 3) . '.' . substr($fornecedor->cnpj, 5, 3) . '/' . substr($fornecedor->cnpj, 8, 4) . '-' . substr($fornecedor->cnpj, 12, 2) }}
                                </td>
                                <td>{{ $fornecedor->pais_origem }}</td>

                                <td class="d-flex  justify-content-around">

                                    <button data-toggle="modal" data-target="#fornecedorModal"
                                        data-route="{{ route('fornecedor.update', $fornecedor->id) }}" type="button"
                                        data-nome="{{ $fornecedor->nome }}" data-cnpj="{{ $fornecedor->cnpj }}"
                                        data-pais_origem="{{ $fornecedor->pais_origem }}"
                                        data-logradouro="{{ $fornecedor->logradouro }}" data-numero="{{ $fornecedor->numero }}"
                                        data-complemento="{{ $fornecedor->complemento }}" data-cidade="{{ $fornecedor->cidade }}"
                                        data-estado="{{ $fornecedor->estado }}"
                                        data-nome_contato="{{ $fornecedor->nome_contato }}"
                                        data-email_contato="{{ $fornecedor->email_contato }}"
                                        data-telefone_contato="{{ $fornecedor->telefone_contato }}"
                                        class="btn btn-warning mr-1 editModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST"
                                        action="{{ route($cliente->deleted_at == null ? 'fornecedor.destroy' : 'fornecedor.ativar', $cliente->id) }}"
                                        id="delete-form" enctype="multipart/form-data">
                                        @method('DELETE')
                                        @csrf
                                        <button type="button" data-id="{{ $fornecedor->id }}"
                                            class="btn btn-danger removeFornecedor"><i class="fa fa-power-off"></i></button>

                                    </form>



                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-not-found />
            @endif
        </div>
    </div>
    <div class="modal fade" id="fornecedorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span>Adicionar</span> fornecedor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fornecedorModal" action="{{ route('fornecedor.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                        <div class="row">
                            <div class="col-3 form-group">
                                <label class="form-label">Nome</label>
                                <input class="form-control" name="nome" id="nome">
                                @error('nome')
                                    <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>


                            <div class="col-2 form-group">
                                <label class="form-label">CNPJ</label>
                                <input class="form-control" name="cnpj" id="cnpj">
                                @error('cnpj')
                                    <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>

                            <div class="col-3 form-group">
                                <label class="form-label">País de Origem</label>
                                <select name="pais_origem" class="form-control select2 w-100" id="paises">
                                    <option value="" selected>Selecione um país</option>
                                    @php
                                        $paises = [
                                            'Afeganistão',
                                            'África do Sul',
                                            'Albânia',
                                            'Alemanha',
                                            'Andorra',
                                            'Angola',
                                            'Antiga e Barbuda',
                                            'Arábia Saudita',
                                            'Argélia',
                                            'Argentina',
                                            'Arménia',
                                            'Austrália',
                                            'Áustria',
                                            'Azerbaijão',
                                            'Bahamas',
                                            'Bangladexe',
                                            'Barbados',
                                            'Barém',
                                            'Bélgica',
                                            'Belize',
                                            'Benim',
                                            'Bielorrússia',
                                            'Bolívia',
                                            'Bósnia e Herzegovina',
                                            'Botsuana',
                                            'Brasil',
                                            'Brunei',
                                            'Bulgária',
                                            'Burquina Faso',
                                            'Burúndi',
                                            'Butão',
                                            'Cabo Verde',
                                            'Camarões',
                                            'Camboja',
                                            'Canadá',
                                            'Catar',
                                            'Cazaquistão',
                                            'Chade',
                                            'Chile',
                                            'China',
                                            'Chipre',
                                            'Colômbia',
                                            'Comores',
                                            'Congo-Brazzaville',
                                            'Coreia do Norte',
                                            'Coreia do Sul',
                                            'Cosovo',
                                            'Costa do Marfim',
                                            'Costa Rica',
                                            'Croácia',
                                            'Cuaite',
                                            'Cuba',
                                            'Dinamarca',
                                            'Dominica',
                                            'Egito',
                                            'Emirados Árabes Unidos',
                                            'Equador',
                                            'Eritreia',
                                            'Eslováquia',
                                            'Eslovénia',
                                            'Espanha',
                                            'Estado da Palestina',
                                            'Estados Unidos',
                                            'Estónia',
                                            'Etiópia',
                                            'Fiji',
                                            'Filipinas',
                                            'Finlândia',
                                            'França',
                                            'Gabão',
                                            'Gâmbia',
                                            'Gana',
                                            'Geórgia',
                                            'Granada',
                                            'Grécia',
                                            'Guatemala',
                                            'Guiana',
                                            'Guiné',
                                            'Guiné Equatorial',
                                            'Guiné-Bissau',
                                            'Haiti',
                                            'Honduras',
                                            'Hong Kong',
                                            'Hungria',
                                            'Iémen',
                                            'Ilhas Marechal',
                                            'Índia',
                                            'Indonésia',
                                            'Irão',
                                            'Iraque',
                                            'Irlanda',
                                            'Islândia',
                                            'Israel',
                                            'Itália',
                                            'Jamaica',
                                            'Japão',
                                            'Jibuti',
                                            'Jordânia',
                                            'Laus',
                                            'Lesoto',
                                            'Letónia',
                                            'Líbano',
                                            'Libéria',
                                            'Líbia',
                                            'Listenstaine',
                                            'Lituânia',
                                            'Luxemburgo',
                                            'Macedónia do Norte',
                                            'Madagáscar',
                                            'Malásia',
                                            'Maláui',
                                            'Maldivas',
                                            'Mali',
                                            'Malta',
                                            'Marrocos',
                                            'Maurícia',
                                            'Mauritânia',
                                            'México',
                                            'Mianmar',
                                            'Micronésia',
                                            'Moçambique',
                                            'Moldávia',
                                            'Mónaco',
                                            'Mongólia',
                                            'Montenegro',
                                            'Namíbia',
                                            'Nauru',
                                            'Nepal',
                                            'Nicarágua',
                                            'Níger',
                                            'Nigéria',
                                            'Noruega',
                                            'Nova Zelândia',
                                            'Omã',
                                            'Países Baixos',
                                            'Palau',
                                            'Panamá',
                                            'Papua Nova Guiné',
                                            'Paquistão',
                                            'Paraguai',
                                            'Peru',
                                            'Polónia',
                                            'Portugal',
                                            'Quénia',
                                            'Quirguistão',
                                            'Quiribáti',
                                            'Reino Unido',
                                            'República Centro-Africana',
                                            'República Checa',
                                            'República Democrática do Congo',
                                            'República Dominicana',
                                            'Roménia',
                                            'Ruanda',
                                            'Rússia',
                                            'Salomão',
                                            'Salvador',
                                            'Samoa',
                                            'Santa Lúcia',
                                            'São Cristóvão e Neves',
                                            'São Marinho',
                                            'São Tomé e Príncipe',
                                            'São Vicente e Granadinas',
                                            'Seicheles',
                                            'Senegal',
                                            'Serra Leoa',
                                            'Sérvia',
                                            'Singapura',
                                            'Síria',
                                            'Somália',
                                            'Sri Lanca',
                                            'Suazilândia',
                                            'Sudão',
                                            'Sudão do Sul',
                                            'Suécia',
                                            'Suíça',
                                            'Suriname',
                                            'Tailândia',
                                            'Taiuão',
                                            'Tajiquistão',
                                            'Tanzânia',
                                            'Timor-Leste',
                                            'Togo',
                                            'Tonga',
                                            'Trindade e Tobago',
                                            'Tunísia',
                                            'Turcomenistão',
                                            'Turquia',
                                            'Tuvalu',
                                            'Ucrânia',
                                            'Uganda',
                                            'Uruguai',
                                            'Usbequistão',
                                            'Vanuatu',
                                            'Vaticano',
                                            'Venezuela',
                                            'Vietname',
                                            'Zâmbia',
                                            'Zimbábué',
                                        ];

                                    @endphp
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais }}">{{ $pais }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-3 form-group">
                                <label class="form-label">Logradouro</label>
                                <input class="form-control" name="logradouro">
                            </div>
                            <div class="col-1 form-group">
                                <label class="form-label">Número</label>
                                <input class="form-control" name="numero">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label class="form-label">Complemento</label>
                                <input class="form-control" name="complemento">
                            </div>
                            <div class="col-3 form-group">
                                <label class="form-label">Cidade</label>
                                <input class="form-control" name="cidade">
                            </div>

                            <div class=" col-2 form-group">
                                <label class="form-label">Estado</label>
                                <input class="form-control" name="estado">
                            </div>

                        </div>
                        <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="mb-3">
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4>Contato</h4>
                            </div>
                            <div class="col-4">
                                <label for="exampleInputEmail1" class="form-label">Nome</label>
                                <input class="form-control" name="nome_contato" id="nome_contato">
                                @error('nome_contato')
                                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="form-label">Email</label>
                                    <input class="form-control" name="email_contato" id="email_contato">

                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="form-label">Telefone</label>
                                    <input class="form-control" name="telefone_contato" id="telefone_contato">
                                    @error('telefone_contato')
                                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>

                    </div>


                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('input[name=cnpj]').mask('99.999.999/9999-99');
            $('.select2').select2({
                placeholder: 'Selecione um país',
                allowClear: true,
                width: '100%'
            });
        });
        $(document).on('click', '.removeFornecedor', function() {
            Swal.fire({
                title: 'Você tem certeza que deseja excluir este registro?',
                text: 'Esta ação não poderá ser desfeita! Todos os produtos desse fornecedor serão excluídos e consequentemente os produtos que estiverem em processos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const id = this.dataset.id;
                    const deleteUrl = `/fornecedor/${id}`;
                    $('#delete-form').attr('action', deleteUrl);

                    $('#delete-form').submit();
                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        });

        $('.editModal').on('click', function(event) {
            var button = $(event.currentTarget)
            var route = button.data('route')
            var modal = $('#fornecedorModal')
            modal.find('form').attr('action', route)
            modal.find('.modal-title span').text('Editar')
            if (modal.find('input[name="_method"]').length === 0) {
                modal.find('form').prepend('<input type="hidden" name="_method" value="put">');
            } else {
                modal.find('input[name="_method"]').val('put');
            }
            var cnpj = button.data('cnpj');
            if (cnpj) {
                cnpj = String(cnpj);
                cnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
                modal.find('input[name="cnpj"]').val(cnpj);
            }
            modal.find('input[name="nome"]').val(button.data('nome'));
            
            modal.find('select[name="pais_origem"]').val(button.data('pais_origem')).trigger('change');
            modal.find('input[name="logradouro"]').val(button.data('logradouro'));
            modal.find('input[name="numero"]').val(button.data('numero'));
            modal.find('input[name="complemento"]').val(button.data('complemento'));
            modal.find('input[name="cidade"]').val(button.data('cidade'));
            modal.find('input[name="estado"]').val(button.data('estado'));
            modal.find('input[name="nome_contato"]').val(button.data('nome_contato'));
            modal.find('input[name="email_contato"]').val(button.data('email_contato'));
            modal.find('input[name="telefone_contato"]').val(button.data('telefone_contato'));
            
        });
    </script>
@endsection

@push('scripts')
@endpush
