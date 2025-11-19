@php
    $fornecedoresEsboco = $fornecedoresEsboco ?? collect();
    $podeSelecionarFornecedor = $podeSelecionarFornecedor ?? false;
@endphp

<div class="mb-4">
    <div class="alert alert-info small">
        <strong>Campos que precisam ser digitados neste esboço:</strong>
        <span class="d-block mt-1">
            Fornecedor da NF, Nome/Razão Social do transportador, Endereço do transportador, Município do transportador,
            CNPJ/CPF do transportador e Informações complementares.
        </span>
    </div>

    <form id="formEsboco" class="card shadow-sm p-3" method="POST" action="{{ route('processo.update', $processo->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Fornecedor que constará na NF</label>
                <select id="fornecedor_esboco" name="fornecedor_id"
                    class="form-control select2 w-100"
                    data-placeholder="{{ $podeSelecionarFornecedor ? 'Selecione um fornecedor' : 'Adicione produtos para liberar' }}"
                    {{ $podeSelecionarFornecedor ? '' : 'disabled' }}>
                    <option value="">{{ $podeSelecionarFornecedor ? '' : 'Adicione produtos para liberar' }}</option>
                    @foreach ($fornecedoresEsboco as $fornecedor)
                        <option value="{{ $fornecedor->id }}"
                            {{ $processo->fornecedor_id == $fornecedor->id ? 'selected' : '' }}>
                            {{ $fornecedor->nome }}
                        </option>
                    @endforeach
                </select>
                @unless($podeSelecionarFornecedor)
                    <small class="text-muted d-block mt-1">Selecione e salve ao menos um produto antes de escolher o fornecedor.</small>
                @endunless
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">CNPJ / CPF da Transportadora</label>
                <input type="text" class="form-control" name="transportadora_cnpj"
                    value="{{ old('transportadora_cnpj', $processo->transportadora_cnpj) }}"
                    placeholder="Somente números">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nome / Razão Social da Transportadora</label>
                <input type="text" class="form-control" name="transportadora_nome"
                    value="{{ old('transportadora_nome', $processo->transportadora_nome) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Município da Transportadora</label>
                <input type="text" class="form-control" name="transportadora_municipio"
                    value="{{ old('transportadora_municipio', $processo->transportadora_municipio) }}">
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">Endereço da Transportadora</label>
                <input type="text" class="form-control" name="transportadora_endereco"
                    value="{{ old('transportadora_endereco', $processo->transportadora_endereco) }}">
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <label class="form-label fw-bold">Informações Complementares (NF)</label>
                <textarea class="form-control" rows="3" name="info_complementar_nf"
                    placeholder="Texto que será exibido na seção 'Infos Complementares' do PDF">{{ old('info_complementar_nf', $processo->info_complementar_nf) }}</textarea>
                <small class="text-muted d-block mt-1">Ao preencher este campo, o texto automático do PDF será substituído.</small>
            </div>
        </div>

        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary" id="btnSalvarEsboco">
                <i class="fas fa-save me-2"></i>Salvar dados do esboço
            </button>
        </div>
    </form>
</div>

<iframe id="pdf-iframe" src="{{ $pdfRoute }}" width="100%"
    height="800px" frameborder="0"></iframe>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery) {
            const $tab = $('#custom-tabs-four-home');
            const fornecedorSelect = $('#fornecedor_esboco');
            if (fornecedorSelect.length && typeof fornecedorSelect.select2 === 'function') {
                fornecedorSelect.select2({
                    width: '100%',
                    placeholder: fornecedorSelect.data('placeholder') || 'Selecione',
                    dropdownParent: $tab
                });
            }
        }

        const formEsboco = document.getElementById('formEsboco');
        if (!formEsboco) {
            return;
        }

        const submitButton = document.getElementById('btnSalvarEsboco');

        formEsboco.addEventListener('submit', async function(event) {
            event.preventDefault();
            if (!submitButton) return;

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Salvando...';

            const formData = new FormData(formEsboco);
            formData.set('_method', 'PUT');

            const parseJsonOrThrow = async (response) => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    return await response.json();
                }
                const text = await response.text();
                throw new Error(text || 'Resposta inesperada do servidor.');
            };

            try {
                const response = await fetch(formEsboco.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await parseJsonOrThrow(response);
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Não foi possível salvar os dados.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Dados atualizados!',
                    timer: 1800,
                    showConfirmButton: false
                });

                const iframe = document.getElementById('pdf-iframe');
                if (iframe) {
                    const url = new URL(iframe.src);
                    url.searchParams.set('_', Date.now());
                    iframe.src = url.toString();
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao salvar',
                    text: error.message || 'Verifique os dados e tente novamente.'
                });
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Salvar dados do esboço';
            }
        });
    });
</script>
@endpush

