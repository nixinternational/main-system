<div class="modal fade" id="produtoModal" tabindex="-1" aria-labelledby="produtoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="produtoModalLabel">
                    Lote de:  <b> <span id="nomeProduto"></span></b>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProduto">
                    <div class="row">
                        <div class="col-2">
                            <label for="exampleInputEmail1" class="form-label">Lote</label>
                            <input class="form-control" id="loteProduto" disabled>
                        </div>
                        <div class="col-2">
                            <label for="exampleInputEmail1" class="form-label">Unidade</label>
                            <input class="form-control" id="unidadeMedida" disabled>
                        </div>

                        <div class="col-2">
                            <label for="exampleInputEmail1" class="form-label">Preço de custo</label>
                            <input class="form-control" id="precoCusto" disabled>
                        </div>
                        <div class="col-2">
                            <label for="exampleInputEmail1" class="form-label">Preço de Venda</label>
                            <input class="form-control" id="precoVenda" disabled>
                        </div>

                        <div class="col-4">
                            <label for="exampleInputEmail1" class="form-label">Fornecedor</label>
                            <input class="form-control" id="fornecedorNome" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1" class="form-label">Marca</label>
                            <input class="form-control" id="marca" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1" class="form-label">Categoria</label>
                            <input class="form-control" id="categoriaProduto" disabled>
                        </div>
                        <div class="col-4">
                            <label for="exampleInputEmail1" class="form-label">Data de Entrada</label>
                            <input class="form-control" id="dataEntrada" disabled>
                        </div>

                        <div class="col-3">
                            <label for="exampleInputEmail1" class="form-label">Data de Fabricação</label>
                            <input class="form-control" id="dataFabricacao" disabled>
                        </div>

                        <div class="col-3">
                            <label for="exampleInputEmail1" class="form-label">Data de validade</label>
                            <input class="form-control" id="dataValidade" disabled>
                        </div>

                        <div class="col-6">
                            <label for="exampleInputEmail1" class="form-label">Responsável</label>
                            <input class="form-control" id="responsavel" disabled>
                        </div>

                        <div class="col-12 ">
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
                    <div class=" d-flex flex-row bg-light mt-2 p-2 rounded">
                        <div class="d-none bg-success rounded px-2 py-1 mr-1">Vendido em <span
                                id="diasVendido"></span> dia(s)
                        </div>
                        <div class="d-none  rounded px-2 py-1 mr-1" id="produtoVencido"></div>

                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>

