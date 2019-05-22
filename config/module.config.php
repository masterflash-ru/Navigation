<?php
/**
генератор ссылок навигации (меню, крошки....)
 */

namespace Mf\Navigation;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
  /*конфиг по умолчанию, создайте аналогичный ключ в глобальном конфиге с нужными типами меню*/
  "menu"=>[
  ],

    'view_helpers' => [
        'factories' => [
            /*Рабочие помощники*/
            View\Helper\Menu::class => View\Helper\Factory\HelperFactory::class,
            View\Helper\Breadcrumbs::class => View\Helper\Factory\HelperFactory::class,
            View\Helper\Pagination::class => InvokableFactory::class,
            
            /*технологические помощники, которые собственно генерируют номера страниц*/
            View\Helper\Simba\Pagination::class => InvokableFactory::class,
            View\Helper\Bootstrap4\Pagination::class => InvokableFactory::class,
            /*Подменный прокси navigation для не ZF3 генераторов ссылок*/
            Service\Bootstrap4Navigation::class => Service\NavigationFactory::class,
        ],
        'aliases' => [
            'Menu' => View\Helper\Menu::class,
            'menu' => View\Helper\Menu::class,
            'Breadcrumbs' =>View\Helper\Breadcrumbs::class,
            'breadcrumbs' =>View\Helper\Breadcrumbs::class,
            "bootstrap4Navigation" => Service\Bootstrap4Navigation::class,
            'Pagination' => View\Helper\Pagination::class,
            'pagination' => View\Helper\Pagination::class,
        ],
    ],
    /*описатели интерфейсов*/
    "interface"=>[
        "menu"=>__DIR__."/admin.menu.php",
    ],
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
            Service\Admin\JqGrid\Plugin\GetMvcUrls::class => Service\Admin\JqGrid\Plugin\FactoryGetMvcUrls::class,
        ],
        'aliases' => [
            "GetMvcUrls"=>Service\Admin\JqGrid\Plugin\GetMvcUrls::class,
        ],
    ],
    /*плагины для ZformPlugin используется в сетке в toolbar*/
    "ZformPlugin"=>[
        'factories' => [
            Service\Admin\Zform\Plugin\MenuNames::class => Service\Admin\Zform\Plugin\FactoryMenuNames::class,
            Service\Admin\Zform\Plugin\ToolBarInit::class => Service\Admin\Zform\Plugin\FactoryToolBarInit::class,
        ],
    ],

];
