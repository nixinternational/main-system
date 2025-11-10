@extends('layouts.app')
@section('title', isset($cliente) ? "Fornecedores de $cliente->nome" : 'Cadastrar  Fornecedor')

@section('content')
    <div class="card">

        <div class="card-text p-3">
            @if (isset($cliente) && !$cliente->fornecedores->isEmpty())

                <div class="table-responsive">
                    <table id="fornecedorTable" class="table table-striped table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
                            <tr>
                                <th class="text-white">Nome</th>
                                <th class="text-white">CNPJ</th>
                                <th class="text-white">País</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                    <tbody>
                        @foreach ($cliente->fornecedores as $fornecedor)
                            <tr @if ($fornecedor->deleted_at != null) style="background-color:#ff8e8e" @endif>
                                <td>{{ $fornecedor->nome }}</td>
                                <td>{{ substr($fornecedor->cnpj, 0, 2) . '.' . substr($fornecedor->cnpj, 2, 3) . '.' . substr($fornecedor->cnpj, 5, 3) . '/' . substr($fornecedor->cnpj, 8, 4) . '-' . substr($fornecedor->cnpj, 12, 2) }}
                                </td>
                                <td>{{ $fornecedor->pais_origem }}</td>

                                <td>
                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                        <button data-toggle="modal" data-target="#fornecedorModal"
                                            data-route="{{ route('fornecedor.update', $fornecedor->id) }}" type="button"
                                            data-nome="{{ $fornecedor->nome }}" data-cnpj="{{ $fornecedor->cnpj }}"
                                            data-pais_origem="{{ $fornecedor->pais_origem }}"
                                            data-logradouro="{{ $fornecedor->logradouro }}"
                                            data-numero="{{ $fornecedor->numero }}"
                                            data-complemento="{{ $fornecedor->complemento }}"
                                            data-cidade="{{ $fornecedor->cidade }}" data-estado="{{ $fornecedor->estado }}"
                                            data-nome_contato="{{ $fornecedor->nome_contato }}"
                                            data-email_contato="{{ $fornecedor->email_contato }}"
                                            data-telefone_contato="{{ $fornecedor->telefone_contato }}"
                                            class="btn btn-warning btn-sm editModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                    <form method="POST"
                                        action="{{ route($cliente->deleted_at == null ? 'fornecedor.destroy' : 'fornecedor.ativar', $cliente->id) }}"
                                        class="delete-form-fornecedor" enctype="multipart/form-data">
                                        @method('DELETE')
                                        @csrf
                                        <button type="button" data-id="{{ $fornecedor->id }}"
                                            class="btn btn-danger btn-sm removeFornecedor" title="Excluir">
                                            <i class="fa fa-power-off"></i>
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <x-not-found />
            @endif
        </div>
    </div>
    <div class="modal fade" id="fornecedorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
                    <h5 class="modal-title text-white" id="exampleModalLabel">
                        <i class="fas fa-truck me-2"></i><span>Adicionar</span> fornecedor
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fornecedorForm" action="{{ route('fornecedor.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        @csrf
                        @if (isset($cliente))
                            <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                        @endif
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

    <style>
        /* Garantir que o campo de busca do Select2 não esteja bloqueado */
        .select2-search__field {
            pointer-events: auto !important;
            cursor: text !important;
            opacity: 1 !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa !important;
            padding: 5px !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            outline: none !important;
            border-color: #5897fb !important;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            $('input[name=cnpj]').mask('99.999.999/9999-99');
            
            // Inicializar Select2 quando o modal for aberto
            $('#fornecedorModal').on('shown.bs.modal', function() {
                var $select = $('#paises');
                
                // Verificar se o elemento existe
                if ($select.length === 0) {
                    console.error('Elemento #paises não encontrado');
                    return;
                }
                
                // Destruir qualquer instância anterior do Select2
                if ($select.hasClass('select2-hidden-accessible')) {
                    try {
                        $select.select2('destroy');
                    } catch(e) {
                        console.log('Erro ao destruir Select2:', e);
                    }
                }
                
                // Remover classes do Select2 se existirem
                $select.removeClass('select2-hidden-accessible');
                $select.attr('data-select2-id', null);
                
                // Limpar qualquer wrapper do Select2
                $select.siblings('.select2-container').remove();
                
                // Aguardar um pouco para garantir que o DOM está pronto
                setTimeout(function() {
                    try {
                        $select.select2({
                            placeholder: 'Selecione um país',
                            allowClear: true,
                            width: '100%',
                            dropdownAutoWidth: true,
                            language: {
                                noResults: function() {
                                    return "Nenhum resultado encontrado";
                                },
                                searching: function() {
                                    return "Buscando...";
                                },
                                inputTooShort: function() {
                                    return "Digite para buscar";
                                }
                            },
                            minimumResultsForSearch: 0,
                            minimumInputLength: 0
                        });
                        
                        // Quando o dropdown abrir, garantir que o campo de busca esteja habilitado
                        $select.on('select2:open', function() {
                            setTimeout(function() {
                                var $searchField = $('.select2-search__field');
                                if ($searchField.length) {
                                    $searchField.prop('disabled', false)
                                               .prop('readonly', false)
                                               .css('pointer-events', 'auto')
                                               .focus();
                                }
                            }, 50);
                        });
                    } catch(e) {
                        console.error('Erro ao inicializar Select2:', e);
                    }
                }, 200);
            });
            
            // Limpar Select2 e todos os inputs quando o modal for fechado
            $('#fornecedorModal').on('hidden.bs.modal', function() {
                // Destruir Select2 se existir
                if ($('#paises').hasClass('select2-hidden-accessible')) {
                    $('#paises').select2('destroy');
                }
                
                // Limpar todos os inputs do formulário
                var form = $('#fornecedorForm')[0];
                if (form) {
                    form.reset();
                    // Limpar também o Select2 manualmente
                    $('#paises').val('').trigger('change');
                    // Limpar todos os inputs de texto
                    $('#fornecedorModal input[type="text"]').val('');
                    $('#fornecedorModal input[type="email"]').val('');
                    $('#fornecedorModal textarea').val('');
                    // Remover método PUT se existir
                    $('#fornecedorModal input[name="_method"]').remove();
                    // Atualizar título do modal
                    $('#fornecedorModal .modal-title span').text('Adicionar');
                }
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
                    const form = $(this).closest('.delete-form-fornecedor');
                    form.attr('action', deleteUrl);
                    form.submit();
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
            modal.find('#fornecedorForm').attr('action', route)
            modal.find('.modal-title span').text('Editar')
            if (modal.find('input[name="_method"]').length === 0) {
                modal.find('#fornecedorForm').prepend('<input type="hidden" name="_method" value="put">');
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
