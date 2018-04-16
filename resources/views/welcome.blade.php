@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('inicio'))
@section('content')
    <div id="app">
        <div class="col-md-6">

            <div class="col-sm-6 col-md-4 col-lg-6 col-lg-offset-2">

                <div class="thumbnail">
                    <img src="{{asset('images/fdg-logo.png')}}" alt="logo">
                </div>

                    <div class="caption">

                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" >
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1"></li>

                            </ol>

                            <!-- Wrapper for slides -->
                            <div class="carousel-inner" role="listbox">
                                <div class="item active">
                                    <img src="{{asset('images/slider/business.jpg')}}" alt="...">
                                    <div class="carousel-caption">
                                    </div>
                                </div>
                                <div class="item">
                                    <img src="{{asset('images/slider/balance.jpg')}}" alt="...">
                                    <div class="carousel-caption">

                                    </div>
                                </div>

                            </div>

                            <!-- Controls -->
                            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>

                        <h3>Sistema de Gestión del POA</h3>

                        <div class="pull-right">
                            @if (Auth::guest())
                                <a href="{{ route('login') }}" class="btn btn-primary" role="button">Entrar


                                        <i class="fa fa-sign-in"></i>

                                </a>

                            @else
                                <a href="{{ route('logout') }}" class="btn btn-danger" role="button">Salir <i class="fa fa-sign-out"></i></a>
                            @endif
                        </div>



                    </div>

                </div>
            </div>


            <div class="col-sm-6 col-md-4 col-lg-6">
                <canvas id="myChart"></canvas>
            </div>

    </div>



    <script>

        var ctx = document.getElementById("myChart");
        var barChartData = {
            labels: [
//
            @foreach($resumenArray as $areas)
                "{{$areas['labels']}}", // "Administ", "TH", "Financiero", "Marketin", "DTM", "Infraest"
            @endforeach

        ],//areas
            datasets: [{
                label: '% Ejecutado',
                backgroundColor:
                    'rgba(75, 192, 192, 1)'
                ,
                borderColor:
                    'rgba(75, 192, 192, 1)'
                ,
                data: [
                    @foreach($resumenArray as $areas)
                        "{{$areas['eje']}}",
                    @endforeach
                ]
            }, {
                label: '% No ejecutado',
                backgroundColor:  'rgba(228, 31, 31, 1)',
                borderColor: 'rgba(187, 21, 21, 1)',
                data: [
                    @foreach($resumenArray as $areas)
                        "{{$areas['no_eje']}}",
                    @endforeach
                ]
            }]

        };

        // This is the important part
        var options = {
            pointLabelFontSize : 2
        };

        var myChart = new Chart(ctx, {

            type: 'bar',

            data: barChartData,
            options: {
                scales: {
                    yAxes: [{
//                    stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            offsetGridLines: true
                        },
                        ticks: {
                            fontSize:9
                        }
                    }]
                },
                responsiveAnimationDuration: 1000,
                title: {
                    display: true,
                    text: 'EJECUCION POA FEDEGUAYAS. "{{$month ? $month->month: ''}}"'
                }
            }
        });
    </script>

@endsection
