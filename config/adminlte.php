<?php

/**
 * Az AdminLTE sablon konfigurációs fájlja.
 * Itt állíthatók be a felület megjelenési és működési paraméterei.
 */
return [

    /**
     * Az oldal címe és formátuma.
     */
    'title' => 'Webgalamb',
    'title_prefix' => '',
    'title_postfix' => '',

    /**
     * Favicon beállítások.
     */
    'use_ico_only' => false,
    'use_full_favicon' => false,

    /**
     * Google betűtípusok engedélyezése.
     */
    'google_fonts' => [
        'allowed' => true,
    ],

    /**
     * Az admin felület logója.
     */
    'logo' => '<b>WG</b>Queue',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /**
     * Bejelentkező felület egyedi logója.
     */
    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /**
     * Oldal betöltés jelző animáció.
     */
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /**
     * Felhasználói menü beállításai a fejlécben.
     */
    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /**
     * Az elrendezés (layout) beállításai.
     */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /**
     * Hitelesítési nézetek CSS osztályai.
     */
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /**
     * Az admin panel különböző elemeinek CSS osztályai.
     */
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /**
     * Az oldalsáv (sidebar) beállításai.
     */
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /**
     * Jobb oldali sáv beállításai.
     */
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /**
     * Az admin felületen használt alapvető URL címek.
     */
    'use_route_url' => true,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password.request',
    'password_email_url' => 'password.email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /**
     * Asset bundling beállítások (Vite/Mix).
     */
    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'resources/css/app.css',
    'laravel_js_path' => 'resources/js/app.js',

    /**
     * Az admin felület menüszerkezete.
     */
    'menu' => [
        // Keresés és teljes képernyő a fejlécben
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Vezérlőpult
        [
            'text' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fas fa-fw fa-cog',
        ],
        ['header' => 'Webgalamb'],
        [
            'text' => 'Webgalamb kiküldések',
            'icon' => 'fas fa-fw fa-envelope',
            'submenu' => [
                [
                    'text' => 'Naptár nézet',
                    'url'  => 'scheduling/calendar',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                ],
                [
                    'text' => 'Új kiküldés rögzítése',
                    'url'  => 'scheduling/create',
                    'icon' => 'fas fa-fw fa-plus-circle',
                ],
                [
                    'text' => 'Levélkiküldéseim',
                    'url'  => 'scheduling/list',
                    'icon' => 'fas fa-fw fa-list',
                ],
                [
                    'text' => 'Minden kiküldés (Admin)',
                    'url'  => 'scheduling/admin-list',
                    'icon' => 'fas fa-fw fa-users-cog',
                    'can'  => 'admin',
                ],
            ],
        ],
        ['header' => 'Beállítások'],
        [
            'text' => 'Profilom',
            'url' => 'profile',
            'icon' => 'fas fa-fw fa-user',
        ],
        ['header' => 'Rendszer kezelés'],
        [
            'text' => 'Naplózás',
            'icon' => 'fas fa-fw fa-database',
            'can'  => 'admin',
            'submenu' => [
                [
                    'text' => 'Tevékenység napló',
                    'url'  => 'admin/logs/activity',
                    'icon' => 'fas fa-fw fa-history',
                ],
                [
                    'text' => 'Hiba napló',
                    'url'  => 'admin/logs/errors',
                    'icon' => 'fas fa-fw fa-exclamation-triangle',
                ],
            ],
        ],
        [
            'text' => 'Beállítások',
            'url'  => 'admin/settings',
            'icon' => 'fas fa-fw fa-cogs',
            'can'  => 'admin',
        ],
        [
            'text' => 'Felhasználók',
            'url'  => 'admin/users',
            'icon' => 'fas fa-fw fa-users',
            'can'  => 'admin',
        ]
    ],

    /**
     * Menüszűrők a láthatóság és az URL-ek kezeléséhez.
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

    /**
     * Bővítmények (plugins) konfigurálása.
     */
    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
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
    ],

    /**
     * IFrame mód beállításai.
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

    /**
     * Livewire támogatás engedélyezése.
     */
    'livewire' => true,

];
