<?php
/**
генератор ссылок навигации (меню, крошки....)
 */

namespace Mf\Navigation;


return [
  /*конфиг по умолчанию, создайте аналогичный ключ в глобальном конфиге с нужными типами меню*/
  "menu"=>[
  ],

    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\Menu::class,
            Service\Bootstrap4Navigation::class => Service\NavigationFactory::class,
        ],
        'aliases' => [
            'Menu' => View\Helper\Menu::class,
            'menu' => View\Helper\Menu::class,
            "bootstrap4Navigation" => Service\Bootstrap4Navigation::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

];
