@extends('layouts.app')
@section('content')
@hasGroup('admnistrador')



    <script>

$( document ).ready(function() {

fetch(`/getPedidosByYear`, ).then(async (response) => {
        //     const resultado = await response.json();
        //     console.log(resultado);
        //     let label = [];
        //     let dataa = [];
        //     const numeroParaMes = {
        //         1: 'Janeiro',
        //         2: 'Fevereiro',
        //         3: 'Março',
        //         4: 'Abril',
        //         5: 'Maio',
        //         6: 'Junho',
        //         7: 'Julho',
        //         8: 'Agosto',
        //         9: 'Setembro',
        //         10: 'Outubro',
        //         11: 'Novembro',
        //         12: 'Dezembro'
        //     };
        //     for (let item of resultado) {
        //         label.push(numeroParaMes[item.mes])
        //         dataa.push(item.quantidade)
        //     }
        //     const ctx = document.getElementById('myChart');
		// new Chart(ctx, {
        //         type: 'bar',
        //         options: {
        //             responsive: true,
        //             plugins: {

        //                 title: {
        //                     display: true,
        //                     text: 'Pedidos por mês'
        //                 }
        //             }
        //         },
        //         data: {
        //             labels: label,
        //             datasets: [{
        //                 label: '',
        //                 data: dataa

        //             }],

        //         },


        //     });
        // });
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
