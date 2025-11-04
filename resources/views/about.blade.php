<!DOCTYPE html>
<html class="wide wow-animation" lang="en">

<head>
    <title>About us</title>
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
        <!-- Working at CaseCraft-->
        <section class="section novi-background section-lg bg-gray-100">
            <div class="container">
                <div class="row row-50 justify-content-center justify-content-lg-between flex-lg-row-reverse">
                    <div class="col-md-10 col-lg-6 col-xl-5">
                        <h3 class="text-uppercase">Sobre Nosotros</h3>
                        <p class="about-subtitle">En EBEN-EZER, nos enorgullecemos de ser líderes en el sector de la
                            mecánica automotriz, proporcionando servicios de reparación y mantenimiento con la más alta
                            calidad y profesionalismo. Fundada con el compromiso de ofrecer soluciones confiables y
                            eficientes para todos los problemas de su vehículo, nuestra empresa ha establecido un
                            estándar de excelencia en cada trabajo que realizamos.</p><a
                            class="button button-lg button-primary button-winona" href="{{ route('catalog') }}">Ver
                            Catalogo</a>
                    </div>
                    <div class="col-md-10 col-lg-6 col-xl-6"><img class="img-responsive"
                            src="images/careers-1-570x388.jpg" alt="" width="570" height="388" />
                    </div>
                </div>
            </div>
        </section>
        <section class="section novi-background section-lg">
            <div class="container">
                <div class="row row-50 justify-content-center justify-content-lg-between flex-lg-row-reverse">
                    <div class="col-md-10 col-lg-6 col-xl-6"><img class="img-responsive"
                            src="images/careers-1-570x388.jpg" alt="" width="570" height="388" />
                    </div>
                    <div class="col-md-10 col-lg-6 col-xl-5">
                        <h3 class="text-uppercase">Nuestros Valores</h3>
                        <p class="about-subtitle">La confianza, la honestidad y el compromiso con la calidad son los
                            pilares fundamentales de nuestra empresa. Nos esforzamos por establecer relaciones duraderas
                            con nuestros clientes basadas en la transparencia y el respeto. En EBEN-EZER, cada
                            reparación y cada servicio es una oportunidad para demostrar nuestro compromiso con la
                            excelencia.</p><a class="button button-lg button-primary button-winona"
                            href="{{ route('catalog') }}">Ver
                            Catalogo</a>
                    </div>

                </div>
            </div>
        </section>
        <!-- Advantages and Achievements-->
        <section class="section novi-background section-md text-center bg-gray-100">
            <div class="container">
                <h3 class="text-uppercase wow-outer"><span class="wow slideInUp">Por que la gente nos elige </span>
                </h3>
                <p class="wow-outer"><span class="text-width-1 wow slideInDown">Elegir EBEN-EZER significa optar por
                        un servicio mecánico de confianza, con un enfoque en la satisfacción del cliente y la calidad
                        del trabajo. Ofrecemos diagnósticos precisos, soluciones efectivas y un trato personalizado que
                        asegura que cada cliente se sienta valorado y atendido. Ya sea que necesite una revisión
                        rutinaria, una reparación compleja o una mejora en su vehículo, en EBEN-EZER estamos aquí para
                        ayudarle a mantener su automóvil en óptimas condiciones.</span></p>
                <div class="row row-50">
                    <div class="col-6 col-md-4 wow-outer">
                        <!-- Counter Minimal-->
                        <article class="counter-minimal wow slideInUp" data-wow-delay=".1s">
                            <div class="counter-minimal-icon box-chloe__icon novi-icon linearicons-users2"></div>
                            <div class="counter-minimal-main">
                                <div class="counter">{{ $brands->count() }} </div>
                            </div>
                            <h5 class="counter-minimal-title">Marcas</h5>
                        </article>
                    </div>
                    <div class="col-6 col-md-4 wow-outer">
                        <!-- Counter Minimal-->
                        <article class="counter-minimal wow slideInUp" data-wow-delay=".1s">
                            <div class="counter-minimal-icon box-chloe__icon novi-icon linearicons-home-icon3"></div>
                            <div class="counter-minimal-main">
                                <div class="counter">{{ $cant_prod }}</div>
                            </div>
                            <h5 class="counter-minimal-title">Productos</h5>
                        </article>
                    </div>
                    <div class="col-6 col-md-4 wow-outer">
                        <!-- Counter Minimal-->
                        <article class="counter-minimal wow slideInUp" data-wow-delay=".1s">
                            <div class="counter-minimal-icon box-chloe__icon novi-icon linearicons-star"></div>
                            <div class="counter-minimal-main">
                                <div class="counter">10</div>
                            </div>
                            <h5 class="counter-minimal-title">Años de experiencia</h5>
                        </article>
                    </div>
                </div>
            </div>
        </section>
        <section class="section novi-background section-lg text-center">
            <div class="container">
                <h3 class="text-uppercase wow-outer"><span class="wow slideInUp">Nuestro Equipo</span></h3>
                <div class="row row-lg-50 row-35 row-xxl-70 justify-content-center justify-content-lg-start">
                    <div class="col-md-10 col-lg-6 wow-outer">
                        <!-- Profile Creative-->
                        <article class="profile-creative wow slideInLeft">
                            <figure class="profile-creative-figure"><img class="profile-creative-image"
                                    src="images/team-1-270x273.jpg" alt="" width="270" height="273" />
                            </figure>
                            <div class="profile-creative-main">
                                <h5 class="profile-creative-title"><a href="#">Jose Torres</a></h5>
                                <p class="profile-creative-position">Fundador, Mecánico</p>
                                <div class="profile-creative-contacts">
                                    <div class="object-inline"><span
                                            class="icon novi-icon icon-md mdi mdi-phone"></span><a href="tel:#">+56
                                            9 6917 0184</a></div>
                                </div>
                                <p>Fundador de la empresa, Mecánico titulado</p>
                            </div>
                        </article>
                    </div>
                    <div class="col-md-10 col-lg-6 wow-outer">
                        <!-- Profile Creative-->
                        <article class="profile-creative wow slideInLeft" data-wow-delay=".2s">
                            <figure class="profile-creative-figure"><img class="profile-creative-image"
                                    src="images/team-2-270x273.jpg" alt="" width="270" height="273" />
                            </figure>
                            <div class="profile-creative-main">
                                <h5 class="profile-creative-title"><a href="#">Jennifer Carrera</a></h5>
                                <p class="profile-creative-position">Fundadora, Marketing y Ventas</p>
                                <div class="profile-creative-contacts">
                                    <div class="object-inline"><span
                                            class="icon novi-icon icon-md mdi mdi-phone"></span><a href="tel:#">+56
                                            9 9686 8700</a></div>
                                </div>
                                <p>Fundadora de la empresa, Encargada de Marketing y Ventas</p>
                            </div>
                        </article>
                    </div>
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
</body>

</html>
