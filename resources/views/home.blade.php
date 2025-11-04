@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')

@stop

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-6">

            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $clients }}</h3>
                    <p>Clientes</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-person"></i>
                </div>
                <a href="{{ route('client.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $suppliers }}</h3>
                    <p>Proveedores</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-industry"></i>
                </div>
                <a href="{{ route('supplier.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $brands }}</h3>
                    <p>Marcas</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-copyright"></i>
                </div>
                <a href="{{ route('brand.index') }}" class="small-box-footer">Ver...<i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $categories }}</h3>
                    <p>Categorias</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <a href="{{ route('category.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">

            <div class="small-box bg-indigo">
                <div class="inner">
                    <h3>{{ $presentations }}</h3>
                    <p>Presentaciones</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <a href="{{ route('presentation.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-navy">
                <div class="inner">
                    <h3>{{ $products }}</h3>
                    <p>Productos</p>
                </div>
                <div class="icon">
                    <i class="fa-brands fa-product-hunt"></i>
                </div>
                <a href="{{ route('product.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $purchases }}</h3>
                    <p>Compras</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-shop"></i>
                </div>
                <a href="{{ route('purchase.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-olive">
                <div class="inner">
                    <h3>{{ $sales }}</h3>
                    <p>Ventas</p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-money-bill"></i>
                </div>
                <a href="{{ route('sale.index') }}" class="small-box-footer">Ver... <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Indicadores</h1>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header sidebar-dark-primary">
                    <h3 class="card-title">Contador Compras y Ventas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="barChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 764px;"
                            width="764" height="250" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header sidebar-dark-primary">
                    <h3 class="card-title">Suma Totales Compras/Ventas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="barChart2"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 764px;"
                            width="764" height="250" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var areaChartData = {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
                'Noviembre', 'Diciembre'
            ],
            datasets: [{
                    label: 'Compras',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: [{{ $arrPurchases }}]
                },
                {
                    label: 'Ventas',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: false,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: [{{ $arrSales }}]
                },
            ]
        }

        var areaChartData2 = {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
                'Noviembre', 'Diciembre'
            ],
            datasets: [{
                    label: 'Compras',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: [{{ $arrPurchasesTotal }}]
                },
                {
                    label: 'Ventas',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: false,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: [{{ $arrSalesTotal }}]
                },
            ]
        }

        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
        }

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })

        var barChartCanvas2 = $('#barChart2').get(0).getContext('2d')
        var barChartData2 = $.extend(true, {}, areaChartData2)
        var temp2 = areaChartData2.datasets[0]
        var temp3 = areaChartData2.datasets[1]
        barChartData2.datasets[0] = temp2
        barChartData2.datasets[1] = temp3

        var barChartOptions2 = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
        }

        new Chart(barChartCanvas2, {
            type: 'bar',
            data: barChartData2,
            options: barChartOptions2
        })
    </script>

@stop
