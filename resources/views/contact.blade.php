<!DOCTYPE html>
<html class="wide wow-animation" lang="en">

<head>
    <title>Contacts</title>
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
        <section class="section swiper-container swiper-slider swiper-slider-minimal" data-loop="true"
            data-slide-effect="fade" data-autoplay="4759" data-simulate-touch="true">
            <div class="swiper-wrapper">
                <div class="swiper-slide swiper-slide_video" data-slide-bg="img/image-1.jpg">
                    <div class="container">
                        <div class="jumbotron-classic-content">
                            <div class="wow-outer">
                                <div
                                    class="title-docor-text font-weight-bold title-decorated text-uppercase wow slideInLeft text-white">
                                    Diferentes Servicios</div>
                            </div>
                            <h1 class="text-uppercase text-white font-weight-bold wow-outer"><span
                                    class="wow slideInDown" data-wow-delay=".2s">Servicios</span></h1>
                            <p class="text-white wow-outer"><span class="wow slideInDown"
                                    data-wow-delay=".35s">Diferentes Servicios para ustedes al precio justo</span></p>
                            <div class="wow-outer button-outer"><a
                                    class="button button-md button-primary button-winona wow slideInDown" href="#"
                                    data-wow-delay=".4s">Ver Servicios</a></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" data-slide-bg="img/image-2.jpg">
                    <div class="container">
                        <div class="jumbotron-classic-content">
                            <div class="wow-outer">
                                <div
                                    class="title-docor-text font-weight-bold title-decorated text-uppercase wow slideInLeft text-white">
                                    Diferentes Productos</div>
                            </div>
                            <h1 class="text-uppercase text-white font-weight-bold wow-outer"><span
                                    class="wow slideInDown" data-wow-delay=".2s">Productos</span></h1>
                            <p class="text-white wow-outer"><span class="wow slideInDown"
                                    data-wow-delay=".35s">Productos para su vehiculo</span></p>
                            <div class="wow-outer button-outer"><a
                                    class="button button-md button-primary button-winona wow slideInDown" href="#"
                                    data-wow-delay=".4s">Ver Productos</a></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" data-slide-bg="img/image-3.jpg">
                    <div class="container">
                        <div class="jumbotron-classic-content">
                            <div class="wow-outer">
                                <div
                                    class="title-docor-text font-weight-bold title-decorated text-uppercase wow slideInLeft text-white">
                                    mas de 2 años de experiencia</div>
                            </div>
                            <h1 class="text-uppercase text-white font-weight-bold wow-outer"><span
                                    class="wow slideInDown" data-wow-delay=".2s">experiencia</span></h1>
                            <p class="text-white wow-outer"><span class="wow slideInDown" data-wow-delay=".35s">Tenemos
                                    años de experiencia</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination-outer container">
                <div class="swiper-pagination swiper-pagination-modern swiper-pagination-marked"
                    data-index-bullet="true"></div>
            </div>
        </section>
        <section class="section novi-background section-sm">
            <div class="container">
                <div class="layout-bordered">
                    <div class="layout-bordered-item wow-outer">
                        <div class="layout-bordered-item-inner wow slideInUp">
                            <div class="icon novi-icon icon-lg mdi mdi-phone text-primary"></div>
                            <ul class="list-0">
                                <li><a class="link-default" href="tel:#">+56
                                        9 6917 0184</a></li>
                                <li><a class="link-default" href="tel:#">+56
                                        9 9686 8700</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="layout-bordered-item wow-outer">
                        <div class="layout-bordered-item-inner wow slideInUp">
                            <div class="icon novi-icon icon-lg mdi mdi-email text-primary"></div><a
                                class="link-default" href="mailto:#">info@ebenezerautomotriz.cl</a>
                        </div>
                    </div>
                    <div class="layout-bordered-item wow-outer">
                        <div class="layout-bordered-item-inner wow slideInUp">
                            <div class="icon novi-icon icon-lg mdi mdi-map-marker text-primary"></div><a
                                class="link-default" href="#">Av San Gregorio
                                #
                                0160
                                <br> La Granja Santiago de Chile, Chile</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section novi-background bg-gray-100">
            <div class="range justify-content-xl-between">
                <div class="cell-xl-6 align-self-center container">
                    <div class="row">
                        <div class="col-lg-9 cell-inner">
                            <div class="section-lg">
                                <h3 class="text-uppercase wow-outer"><span class="wow slideInDown">Contactanos</span>
                                </h3>
                                <!-- RD Mailform-->
                                <form class="rd-form rd-mailform" data-form-output="form-output-global"
                                    data-form-type="contact" method="post" action="bat/rd-mailform.php">
                                    <div class="row row-10">
                                        <div class="col-md-6 wow-outer">
                                            <div class="form-wrap wow fadeSlideInUp">
                                                <label class="form-label-outside"
                                                    for="contact-first-name">Nombre</label>
                                                <input class="form-input" id="contact-first-name" type="text"
                                                    name="name" data-constraints="">
                                            </div>
                                        </div>
                                        <div class="col-md-6 wow-outer">
                                            <div class="form-wrap wow fadeSlideInUp">
                                                <label class="form-label-outside"
                                                    for="contact-last-name">Apellido</label>
                                                <input class="form-input" id="contact-last-name" type="text"
                                                    name="name" data-constraints="">
                                            </div>
                                        </div>
                                        <div class="col-md-6 wow-outer">
                                            <div class="form-wrap wow fadeSlideInUp">
                                                <label class="form-label-outside" for="contact-email">E-mail</label>
                                                <input class="form-input" id="contact-email" type="email"
                                                    name="email" data-constraints="">
                                            </div>
                                        </div>
                                        <div class="col-md-6 wow-outer">
                                            <div class="form-wrap wow fadeSlideInUp">
                                                <label class="form-label-outside" for="contact-phone">Teléfono</label>
                                                <input class="form-input" id="contact-phone" type="text"
                                                    name="phone" data-constraints="">
                                            </div>
                                        </div>
                                        <div class="col-12 wow-outer">
                                            <div class="form-wrap wow fadeSlideInUp">
                                                <label class="form-label-outside" for="contact-message">Tu
                                                    Mensaje</label>
                                                <textarea class="form-input" id="contact-message" name="message" data-constraints=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="group group-middle">
                                        <div class="wow-outer">
                                            <button id="btn-whatsapp"
                                                class="button button-primary button-winona wow slideInRight"
                                                type="button"><i class="fa-brands fa-whatsapp"></i> Whatsapp</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cell-xl-5 height-fill wow fadeIn">
                    <img src="img/mapa.jpg" alt="">
                </div>
            </div>
        </section>
        @include('site.footer')
    </div>
    <!-- Global Mailform Output-->
    <div class="snackbars" id="form-output-global"></div>
    <!-- Javascript-->
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            let whatsapp = $('#btn-whatsapp');

            whatsapp.click(function() {
                let name = $("#contact-first-name").val();
                let lastname = $("#contact-last-name").val();
                let email = $("#contact-email").val();
                let phone = $("#contact-phone").val();
                let message = $("#contact-message").html();
                let send = "Hola!, soy " + name + " " + lastname + ", mi correo es: " + email +
                    ", mi telefono es: " + phone + " y mi mensaje es: " + message;
                const number = "56969170184";
                const url =
                    "https://api.whatsapp.com/send?phone=" + number + "&texto=" + encodeURIComponent(send);
                window.open(url, "_blank");
            })
        })
    </script>
</body>

</html>
