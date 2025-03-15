<div class="modal fade" id="produtoModal" tabindex="-1" aria-labelledby="produtoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produtoModalLabel">Informações do produto: <b><span
                            id="nomeProduto"></span></b>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProduto" class="mb-2">
                    <div class="row">
                        <div class="col-2">
                            <label for="exampleInputEmail1" class="form-label">Código</label>
                            <input class="form-control" id="codigoProduto" disabled>
                        </div>

                        <div class="col-3">
                            <label for="exampleInputEmail1" class="form-label">Unidade de Medida</label>
                            <input class="form-control" id="unidadeMedida" disabled>
                        </div>

                        <div class="col-3">
                            <label for="exampleInputEmail1" class="form-label">Fornecedor</label>
                            <input class="form-control" id="fornecedorNome" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1" class="form-label">Marca</label>
                            <input class="form-control" id="marca" disabled>
                        </div>
                        <div class="col-6">
                            <label for="exampleInputEmail1" class="form-label">Data de cadastro</label>
                            <input class="form-control" id="dataCadastro" disabled>
                        </div>
                        <div class="col-6">
                            <label for="exampleInputEmail1" class="form-label">Responsável</label>
                            <input class="form-control" id="responsavel" disabled>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="form-floating">
                                <label for="floatingTextarea2">Descrição</label>
                                <textarea class="form-control" id="descricaoProduto" disabled style="height: 100px;resize:none"></textarea>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="row">
                                <div class="col-3">
                                    <label for="exampleInputEmail1" class="form-label">Porção</label>
                                    <input disabled id="porcao" value="" class="form-control">
                                </div>

                                <div class="col-3">
                                    <label for="exampleInputEmail1" class="form-label">Proteína (g)</label>
                                    <input disabled id="proteina" value="" class="form-control">
                                </div>
                                <div class="col-3">
                                    <label for="exampleInputEmail1" class="form-label">Carboidratos (g)</label>
                                    <input disabled id="carboidrato" value="" class="form-control">
                                </div>
                                <div class="col-3">
                                    <label for="exampleInputEmail1" class="form-label">Gorduras Totais (g)</label>
                                    <input disabled id="gordura_total" value="" class="form-control">
                                </div>

                            </div>
                        </div>

                    </div>
                </form>
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
