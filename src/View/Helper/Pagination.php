<?php
/*
* помощник view для вывода навигационных элементов
* генерирует список номеров страниц из объекта Pagination 
*/

namespace Mf\Navigation\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Exception;

/**
 * помощник - вывода меню
 */
class Pagination extends AbstractHelper 
{
    protected $connection;
    protected $cache;
    protected $rs;
    protected $container;
    protected $_default=[
        "Default"=>[/*старый вид*/
            "tpl"=>null,
        ],
        "bootstrap4"=>[ /*параметры стандарта bootstrap4*/
            "tpl"=>null,
        ],
    ];

/*конструктор
*параметры передаются из фабрики
*/
public function __construct ($connection,$cache,$container)
  {
    $this->connection=$connection;
    $this->cache=$cache;
    $this->container=$container;
  }

/*

* пустой массив, по умолчанию используется стандартный помощник ZF3
* 
*/
public function __invoke($sysname,array $options=[])
{
    if (empty($sysname)){
        return $this;
    }
    /*извлекаем локаль, если есть*/
    if (!empty($options["locale"])){
        $locale=$options["locale"];
    } else {
        $locale="ru_RU";
    }
    
    /*получить массив для передачи его в Navigation*/
    $pages=$this->getMenu($sysname,$locale);
    /*генерируем объект Navigation от ZF3*/
    $navigation = $this->createNavigation($pages);
    return $this->render($navigation,$options);
}


/*
* рендеринг меню с опциями
* $navigation - контейнер с навигацией (Zend\Navigation\Navigation)
* $options - массив опций (см.дефолтные), например,
* array ("bootstrap4"=>[
            "locale"=>"ru_RU",               //имя локали
            "ulClass"=>"nav",                //класс для ul элемента
            "indent"=>"",                    //идентификатор, обязательно если несколько меню на сайте
            "minDepth"=>0,                   //минимальный уровень вывода
            "maxDepth"=>null,                //максимальный уровень
            "liActiveClass"=>"active",       //имя класса для активного пункта
            "escapeLabels"=>true,            //экранировать метки да/нет
            "addClassToListItem"=>false,
            "OnlyActiveBranch"=>false,
            "tpl"=>null,
        ])
*/
public function render(ZFNavigation $navigation,array $options)
{
    $options=$this->normalizeOptions($options);
    $view=$this->getView();
    if ($options["menu_type"]=="zf3"){
        /*стандартный из ZF3 прокси navigation*/
        $nav_proxy="Navigation";
    } else {
        /*подменный прокси, в котором прописаны разные виды генерации*/
        $nav_proxy=$options["menu_type"]."Navigation";
    }

    $nav=$view->$nav_proxy();

    $nav =$nav->menu($navigation)
        ->setulClass($options["ulClass"])
        ->setminDepth($options["minDepth"])
        ->setmaxDepth($options["maxDepth"])
        ->setindent($options["indent"])
        ->setliActiveClass($options["liActiveClass"])
        ->escapeLabels($options["escapeLabels"])
        ->setaddClassToListItem($options["addClassToListItem"])
        ->setOnlyActiveBranch($options["OnlyActiveBranch"]);

  if(!empty($options["tpl"]) ){
    //если указан шаблон, то применим его
    return $nav->setPartial($options["tpl"])
            ->renderPartialWithParams($options);
  } 
    //стандартный рендер меню
    return $nav->setPartial(null)->render();
}
    


/*
* нормализация опций, возвращаются опции дополненные значениями по умолчанию 
* возвращает нормализованный массив опций
*/
public function normalizeOptions(array $options)
{
    $menu_type=array_keys($options);
    
    if (empty($menu_type)){
        $menu_type="zf3";
        $options=$this->_default[$menu_type];
    } else {
        $menu_type=strtolower($menu_type[0]);
        if (!isset($this->_default[$menu_type])) {
            throw new  Exception("Не допустимый тип навигации $menu_type");
        }
        if (!isset($options[$menu_type]) || !is_array($options[$menu_type])) {
            throw new  Exception("Не допустимые параметры для генерации навигации");
        }

        $options=array_replace_recursive($this->_default[$menu_type],$options[$menu_type]);
    }
    
    $options["menu_type"]=$menu_type;
    return $options;
}
}