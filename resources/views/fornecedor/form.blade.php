@extends('layouts.app')
@section('title', isset($fornecedor) ? "Editar fornecedor: $fornecedor->nome" : 'Cadastrar fornecedor')

@section('content')
    <div class="card">
        <div class="card-text p-3">
            <form enctype="multipart/form-data"
                action="{{ isset($fornecedor) ? route('fornecedor.update', $fornecedor->id) : route('fornecedor.store') }}"
                method="POST">
                @csrf
                @if (isset($fornecedor))
                    @method('PUT')
                @endif
                <div class="row">
                    {{-- Nome --}}
                    <div class="col-3 form-group">
                        <label class="form-label">Nome</label>
                        <input value="{{ old('nome', $fornecedor->nome ?? '') }}" class="form-control" name="nome"
                            id="nome">
                        @error('nome')
                            <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>

                    {{-- CNPJ (opcional) --}}
                    <div class="col-2 form-group">
                        <label class="form-label">CNPJ</label>
                        <input value="{{ old('cnpj', $fornecedor->cnpj ?? '') }}" class="form-control" name="cnpj"
                            id="cnpj">
                        @error('cnpj')
                            <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>

                    {{-- País de Origem/Aquisição --}}
                    <div class="col-3 form-group">
                        <label class="form-label">País de Origem</label>
                        <select name="pais_origem" class="form-control select2" id="paises">
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
                                <option {{ old('pais_origem', $fornecedor->pais_origem ?? '') == $pais ? 'selected' : '' }}
                                    value="{{ $pais }}">{{ $pais }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-3 form-group">
                        <label class="form-label">Logradouro</label>
                        <input value="{{ old('logradouro', $fornecedor->logradouro ?? '') }}" class="form-control"
                            name="logradouro">
                    </div>
                    <div class="col-1 form-group">
                        <label class="form-label">Número</label>
                        <input value="{{ old('numero', $fornecedor->numero ?? '') }}" class="form-control" name="numero">
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 form-group">
                        <label class="form-label">Complemento</label>
                        <input value="{{ old('complemento', $fornecedor->complemento ?? '') }}" class="form-control"
                            name="complemento">
                    </div>
                    <div class="col-3 form-group">
                        <label class="form-label">Cidade</label>
                        <input value="{{ old('cidade', $fornecedor->cidade ?? '') }}" class="form-control" name="cidade">
                    </div>

                    <div class=" col-2 form-group">
                        <label class="form-label">Estado</label>
                        <input value="{{ old('estado', $fornecedor->estado ?? '') }}" class="form-control" name="estado">
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
                        <input value="{{ isset($fornecedor) ? $fornecedor->nome_contato : '' }}" class="form-control"
                            name="nome_contato" id="nome_contato">
                        @error('nome_contato')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
             
                    <div class="col-2">
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label">Email</label>
                            <input
                                value="{{ isset($fornecedor) ? $fornecedor->email_contato : old('email_contato') ?? '' }}"
                                class="form-control" name="email_contato" id="email_contato">

                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label">Telefone</label>
                            <input
                                value="{{ isset($fornecedor) ? $fornecedor->telefone_contato : old('telefone_contato') ?? '' }}"
                                class="form-control" name="telefone_contato" id="telefone_contato">
                            @error('telefone_contato')
                                <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('input[name=cnpj]').mask('99.999.999/9999-99');
            $('.select2').select2({
                placeholder: 'Selecione um país',
                allowClear: true
            });
        });
    </script>
@endsection

@push('scripts')
@endpush
