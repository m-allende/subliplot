<!DOCTYPE html>
<html class="wide wow-animation" lang="en">

<head>
    <title>Servicio Automotriz EBEN-EZER</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport"
        content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="img/logo.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css"
        href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style-site.css" id="main-styles-link">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css" />
    @csrf
    <style>
        .ie-panel {
            display: none;
            background: #212121;
            padding: 10px 0;
            box-shadow: 3px 3px 5px 0 rgba(0, 0, 0, .3);
            clear: both;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        html.ie-10 .ie-panel,
        html.lt-ie-10 .ie-panel {
            display: block;
        }

        /* styles for '...' */
        .truncate {
            /* hide text if it more than N lines  */
            overflow: hidden;
            /* for set '...' in absolute position */
            position: relative;
            /* use this value to count block height */
            line-height: 1.2em;
            /* max-height = line-height (1.2) * lines max number (3) */
            max-height: 20em;
            /* fix problem when last visible word doesn't adjoin right side  */
            text-align: justify;
            /* place for '...' */
            margin-right: -1em;
            padding-right: 1em;
        }

        /* create the ... */
        .truncate:before {
            /* points in the end */
            content: '...';
            /* absolute position */
            position: absolute;
            /* set position to right bottom corner of block */
            right: 0;
            bottom: 0;
        }

        /* hide ... if we have text, which is less than or equal to max lines
*/
        .truncate:after {
            /* points in the end */
            content: '';
            /* absolute position */
            position: absolute;
            /* set position to right bottom corner of text */
            right: 0;
            /* set width and height */
            width: 1em;
            height: 1em;
            margin-top: 0.2em;
            /* bg color = bg color under block */
            background: white;
        }

        .txtcol a {
            cursor: pointer;
        }

        .txtcol a {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img
                src="images/ie8-panel/warning_bar_0000_us.jpg" height="42" width="820"
                alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a>
    </div>
    <div class="preloader">
        <div class="preloader-logo"><img src="img/logo.png" alt="" width="151" height="44"
                srcset="img/logo.png" style="border-radius: 50%;" />
        </div>
        <div class="preloader-body">
            <div id="loadingProgressG">
                <div class="loadingProgressG" id="loadingProgressG_1"></div>
            </div>
        </div>
    </div>
    <div class="page">
        @include('site.header')

        <div class="row mt-5 ml-3">
            @include('site.filter')
            <div class="col-8">
                <div class="row mt-5">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <form action="">
                                <div class="input-group">
                                    <input id="search" type="text" class="form-control"
                                        placeholder="Buscar por nombre">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent">
                                            <a href="#" id="btn-search"><i class="fa fa-search"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </form>
                            <div class="dropdown ml-4">
                                <div class="input-group">
                                    <span class="input-group-text">Ordenar por :</span>
                                    <select class="form-control" aria-label="Order" id="order-catalog">
                                        <option value="1">Nombre</option>
                                        <option value="2">Ultimos</option>
                                        <option value="3">Popularidad</option>
                                        <option value="4">Precio mayor</option>
                                        <option value="5">Precio menor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <section class="section novi-background section-md text-center">
                    <div class="container">
                        <div id="content-products" class="row row-lg-50 row-35 offset-top-2">

                        </div>
                        <div id="content-pagination" class="col-12 pb-1">

                        </div>
                    </div>
                </section>
            </div>
        </div>

        @include('site.footer')
    </div>
    <!-- Global Mailform Output-->
    <div class="snackbars" id="form-output-global"></div>
    <!-- Javascript-->
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let token = $('meta[name="csrf-token"]').attr('content');
            let take = 12;
            let page = 1;
            let search = "";
            let orderBy = 1;
            let category = 0;
            let brand = 0;
            let price_range = '{{ $prices[0]->price_min }},{{ $prices[0]->price_max }}';

            refreshProducts();

            $(".js-range-slider").ionRangeSlider({
                type: "double",
                min: {{ $prices[0]->price_min }},
                max: {{ $prices[0]->price_max }},
                from: {{ $prices[0]->price_min }},
                to: {{ $prices[0]->price_max }},
                grid: true,
                prefix: "$",
                step: 1000,
                onFinish: function(data) {
                    price_range = data.from + "," + data.to;
                    refreshProducts();
                },
            });

            $('#order-catalog').on('change', function() {
                orderBy = $(this).val();
                refreshProducts();
            });

            $('.category').on('change', function() {
                category = $(this).val();
                refreshProducts();
            });

            $('.brand').on('change', function() {
                brand = $(this).val();
                refreshProducts();
            });

            $('#search').on('keypress', function() {
                search = $(this).val();
                refreshProducts();
            });

            $('#search').on('blur', function() {
                search = $(this).val();
                refreshProducts();
            });

            $(document).on('click', '.page-number', function(e) {
                e.preventDefault();
                page = parseInt($(this).data("param1"));
                refreshProducts();
            })

            $(document).on('click', '.page-prev', function(e) {
                e.preventDefault();
                page = page - 1;
                refreshProducts();
            })

            $(document).on('click', '.page-next', function(e) {
                e.preventDefault();
                page = page + 1;
                refreshProducts();
            })

            $(".txtcol").click(function(e) {
                e.preventDefault();
                if ($(this).prev().hasClass("truncate")) {
                    $(this).children('a').text("Ver Menos");
                } else {
                    $(this).children('a').text("Ver Más");
                }
                $(this).prev().toggleClass("truncate");
            });

            function refreshProducts() {
                Swal.fire({
                    title: "Favor Esperar",
                    timer: 1000000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: function() {
                        Swal.showLoading()
                    }
                });
                data = "&take=" + take;
                data += "&page=" + page;
                data += "&orderby=" + orderBy;
                data += "&search=" + search;
                data += "&price_range=" + price_range;
                data += "&category=" + category;
                data += "&brand=" + brand;
                $.ajax({
                    type: "GET",
                    url: "{{ route('catalog') }}",
                    data: '_token=' + token + data,
                    success: function(data) {
                        drawProducts(data.products);
                        drawPagination(data.count);
                        Swal.close();
                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'error',
                            title: "Error",
                            html: "Error al Grabar"
                        })
                    }
                }); //end ajax
            }

            function drawProducts(products) {
                if (products != null) {
                    let html = "";
                    products.forEach(product => {
                        html += '<div class="col-md-3 wow-outer">' +
                            '<!-- Post Modern-->' +
                            '<article class="post-modern wow slideInLeft "><a class="post-modern-media" href="#">';
                        if (product.last_photo != null) {
                            html +=
                                '<img src="' + product.last_photo.path +
                                '" alt="" width="571"height="353" />';
                        } else {
                            html += '<img src="img/no-image.jpg" alt="" width="571" height="353" />';
                        }
                        html +=
                            ' </a><h4 class="post-modern-title"><a class="post-modern-title" href="#">' +
                            product.name + '</a></h4>';
                        html += ' <ul class="post-modern-meta"><li><a class="button-winona"';
                        html +=
                            ' href="#">$' + parseFloat(product.last_price.price).toLocaleString("de-DE") +
                            '</a>';
                        html += ' </li><li>' + product.brand.name + '</li>';
                        html += ' <li>' + product.category.name + '</li>';
                        html += ' </ul> <p>' + product.description + '</p>';
                        //html +=' <a href="#" class="btn btn-primary mt-2"><i class="fas fa-shopping-cart text-secondary mr-1"></i>Agregar al carro</a>';
                        html += '</article></div>';
                    });
                    $("#content-products").html(html);
                }
            }

            function drawPagination(data) {
                let html = '<nav aria-label="Page navigation" class="mt-5">' +
                    '<ul class="pagination justify-content-center mb-3">' +
                    '<li class="page-item disabled">' +
                    '<a class="page-link page-prev" href="#" aria-label="Previous">' +
                    '<span aria-hidden="true">«</span>' +
                    '<span class="sr-only">Previous</span>' +
                    '</a></li>';
                pages = Math.round(data / take);
                pages = pages == 0 ? 1 : pages;
                let count = 0;
                for (let index = 1; index <= pages; index++) {
                    if (count <= 3 || count >= (pages - 3) || index == page) {
                        if (index == page) {
                            html += '<li class="page-item active"><a class="page-link page-number" data-param1="' +
                                index + '" href="#">' + index +
                                '</a></li>';
                        } else {
                            html += '<li class="page-item"><a class="page-link page-number" data-param1="' +
                                index + '" href="#">' + index + '</a></li>';
                        }
                    } else {
                        index = pages - 3;
                    }
                    count = count + 1;
                }
                html += '<a class="page-link page-next" href="#" aria-label="Next">' +
                    '<span aria-hidden="true">»</span>' +
                    '<span class="sr-only">Next</span>' +
                    '</a></li></ul></nav>';
                $("#content-pagination").html(html);
            }

        })
    </script>
</body>

</html>
