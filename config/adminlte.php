<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Subliplot',
    'title_prefix' => 'Subliplot | ',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Subliplot</b>',
    'logo_img' => 'img/logo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'img/logo.png',
            'alt' => 'Auth Logo',
            'class' => 'brand-image img-circle elevation-3',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'img/logo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => ['xs' => true],
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => ['xs' => true],
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'bg-gradient-dark',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'xs',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => true,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'n',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => '',
    'password_reset_url' => '',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [

        // Encabezado
        ['header' => 'Navegación'],

        // Ir a la tienda pública (abre en nueva pestaña)
        [
            'text'   => 'Ver tienda',
            'icon'   => 'fas fa-store',
            'target' => '_blank',
            'url'    => '/', // tu storefront
        ],

        ['header' => 'Gestión del sistema', 'role' => ['admin']],

        // Ventas Realizadas
        [
            'text'   => 'Ventas Realizadas',
            'icon'   => 'fas fa-cart-shopping',
            'route'  => 'sale.index',
            'active' => ['sales*', 'sale*'],
            'role'   => ['admin'],
        ],

        // Usuarios
        [
            'text'   => 'Usuarios',
            'icon'   => 'fas fa-users',
            'route'  => 'user.index',
            'active' => ['user*'],
            'role'   => ['admin'],
        ],

        // Roles
        [
            'text'   => 'Roles',
            'icon'   => 'fas fa-user-shield',
            // ajusta a tu route si la tienes; si usas sólo AJAX, puedes dejar url => '/role'
            'url'    => '/role', 
            'active' => ['role*'],
            'role'   => ['admin'],
        ],

        // Catálogo
        [
            'text'   => 'Catálogo',
            'icon'   => 'fas fa-boxes-stacked',
            'role'   => ['admin'],
            'submenu' => [
                [
                    'text'   => 'Categorías',
                    'icon'   => 'fas fa-layer-group',
                    'route'  => 'category.index',
                    'active' => ['category*'],
                ],
                [
                    'text'   => 'Productos',
                    'icon'   => 'fas fa-box',
                    'route'  => 'product.index',
                    'active' => ['product*'],
                ],
                [
                    'text' => 'Precios',
                    'route'  => 'product-prices.index',
                    'icon' => 'fas fa-dollar-sign',
                ],
            ],
        ],

        // Atributos (ejemplos: tamaños, tipos de papel, acabados)
        [
            'text'   => 'Atributos',
            'icon'   => 'fas fa-sliders-h',
            'role'   => ['admin'],
            'submenu' => [
                 [
                    'text' => 'Tipos',
                    'url'  => 'attribute-type',
                    'icon' => 'far fa-circle',
                ],
                [
                    'text' => 'Valores',
                    'url'  => 'attribute-value',
                    'icon' => 'far fa-circle',
                ],
            ],
        ],

        // Ventas / Órdenes
        [
            'text'   => 'Órdenes',
            'icon'   => 'fas fa-file-invoice',
            //'route'  => 'order.index',    // si defines resource('order')
            'active' => ['order*'],
            'role'   => ['admin'],
        ],

        // Reportes (opcional)
        [
            'text'   => 'Reportes',
            'icon'   => 'fas fa-chart-line',
            //'route'  => 'report.index',   // crea la ruta si la necesitas
            'active' => ['report*'],
            'role'   => ['admin'],
        ],

        ['header' => 'Mi cuenta'],

        // Menú visible para cualquier usuario logueado
        [
            'text'  => 'Perfil',
            'icon'  => 'fas fa-user',
            //'route' => 'profile.index',   // ajusta a tu ruta de perfil
            'active'=> ['profile*'],
        ],
        [
            'text'  => 'Cerrar sesión',
            'icon'  => 'fas fa-sign-out-alt',
            'route' => 'logout',          // si usas route('logout') por POST, define url y método en tu logout
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                // --- JS base DataTables ---
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js'],

                // --- Buttons core + integración Bootstrap 4 ---
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js'],

                // --- Dependencia para Excel ---
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js'],

                // --- Botones HTML5 / Print / ColVis ---
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js'],

                // --- CSS ---
                ['type' => 'css', 'asset' => false, 'location' => 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css'],
                ['type' => 'css', 'asset' => false, 'location' => 'https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css'],
            ],
        ],

        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'Custom' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '/css/style.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '/css/croppie.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '/js/custom.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '/js/croppie.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => "https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/inputmask.min.js",
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
