@extends('layouts.app')
@section('content')
@hasGroup('admnistrador')

    <div class="row">
        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $pedidosHoje }}</h3>
                    <p>Pedidos de hoje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>

            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pedidosEntregues }}</h3>
                    <p>Pedidos entregues do mês</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>

            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$pedidosAtrasados}}</h3>
                    <p>Pedidos em Atraso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>

            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$pedidosCancelados}}</h3>
                    <p>Pedidos Cancelados do mês</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>

            </div>
        </div>

    </div>

    <script>
        
$( document ).ready(function() {

fetch(`/getPedidosByYear`, ).then(async (response) => {
            const resultado = await response.json();
            console.log(resultado);
            let label = [];
            let dataa = [];
            const numeroParaMes = {
                1: 'Janeiro',
                2: 'Fevereiro',
                3: 'Março',
                4: 'Abril',
                5: 'Maio',
                6: 'Junho',
                7: 'Julho',
                8: 'Agosto',
                9: 'Setembro',
                10: 'Outubro',
                11: 'Novembro',
                12: 'Dezembro'
            };
            for (let item of resultado) {
                label.push(numeroParaMes[item.mes])
                dataa.push(item.quantidade)
            }
            const ctx = document.getElementById('myChart');
		new Chart(ctx, {
                type: 'bar',
                options: {
                    responsive: true,
                    plugins: {

                        title: {
                            display: true,
                            text: 'Pedidos por mês'
                        }
                    }
                },
                data: {
                    labels: label,
                    datasets: [{
                        label: '',
                        data: dataa

                    }],

                },


            });
        });
});

    </script>
        @endhasGroup

    <!-- /.content -->
@endsection

@section('style')
<style>
    .eliasViado{
        background-color: pink;
    }
</style>
@endsection
