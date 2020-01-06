<?php
/*
* помощник view для вывода навигационных элементов
* при необходимости его можно расширить новым объектом для генерации меню из каталога, например.
*/

namespace Mf\Navigation\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use ADO\Service\RecordSet;
use Laminas\Navigation\Service\ConstructedNavigationFactory;
use Laminas\Navigation\Navigation as ZFNavigation;
use Mf\Navigation;
use Exception;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * помощник - вывода меню
 */
class Menu extends AbstractHelper 
{
    protected $connection;
    protected $cache;
    protected $rs;
    protected $container;
    protected $_default=[
        "zf3"=>[/*стандартные параметры для встроенного в ZF3 генератора меню*/
            "locale"=>"ru_RU",               //имя локали
            "ulClass"=>"navigation",         //класс для ul элемента (сдля стандартного Laminas меню)
            "indent"=>"",
            "minDepth"=>0,                   //минимальный уровень вывода
            "maxDepth"=>null,                //максимальный уровень
            "liActiveClass"=>"active",       //имя класса для активного пункта
            "escapeLabels"=>true,            //экранировать метки да/нет
            "addClassToListItem"=>false,
            "OnlyActiveBranch"=>false,
            "tpl"=>null,
        ],
        "bootstrap4"=>[ /*параметры для меню стандарта bootstrap4 (без логотипа и поисковой формы)*/
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
*$options - массив опций (см. выше дефолтные объявления), 
* пустой массив, по умолчанию используется стандартный помощник ZF3
* 
*/
public function __invoke($sysname=null,array $options=[])
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
* генерирует объект Navigation 
* $pages - массив (пока) страниц, пригодный для генерации Navigation
* возвращает Navigation с контейнером
*/
public function createNavigation(array $pages)
{
    $factory    = new ConstructedNavigationFactory($pages);
    return $factory->createService($this->container);
}

/*
* рендеринг меню с опциями
* $navigation - контейнер с навигацией (Laminas\Navigation\Navigation)
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
* получить из базы меню данного имени и сгенерировать дерево в виде массива
* $sysname - системное имя меню,
* $locale - локаль, например, ru_RU
* возвращает массив пригодный для передачи его в Navigation
* результирующий массив кешируется
*/
public function getMenu($sysname,$locale)
{
    $result = false;
    $key="Menu_".preg_replace('/[^0-9a-zA-Z_\-]/iu', '',$sysname)."_{$locale}";

    $menu = $this->cache->getItem($key, $result);
    if (!$result){
        $rs=new RecordSet();
        $rs->MaxRecords=0; 
        $rs->CursorType = adOpenKeyset;
        $rs->open("select * from menu where sysname='{$sysname}' and locale='{$locale}' order by poz",$this->connection);
        
        if ($rs->EOF){return [];}
        
        $array=[];
        while (!$rs->EOF){
            $r=[];
            $r["id"]=$rs->Fields->Item["id"]->Value;
            $r["subid"]=$rs->Fields->Item["subid"]->Value;
            
            $r["label"]=$rs->Fields->Item['label']->Value;
            if ($rs->Fields->Item['url']->Value){
                //если указан URL тогда он ставится, MVC игнорируется
                $r["uri"]=$rs->Fields->Item['url']->Value;
            } else {
                $_mvc=$rs->Fields->Item['mvc']->Value;
                if (!empty($_mvc)) {
                    //если есть MVC тогда добавим текст элемента меню
                    $r=array_merge($r,unserialize($_mvc));
                } else {
                    //если не указан MVC тогда пустая ссылка
                    $r["uri"]="#";
                }
            }
            
            $array[]=$r;
            $rs->MoveNext();
        }
        
        $tree=[];
        
        foreach($array as $cat) {
            $tree[$cat['id']] = $cat;
            unset($tree[$cat['id']]['id']);
        }
        
        $tree['0'] = array(
            'subid' => '',
            'label' => 'Корень',
        );
        
        foreach ($tree as $id => $node) {
            if (isset($node['subid']) && isset($tree[$node['subid']])) {
                $tree[$node['subid']]['pages'][$id] =& $tree[$id];
            }
        }
        //итоговое дерево
        $menu= $tree[0]['pages'];
        $this->cache->setItem($key, $menu);
        $this->cache->setTags($key,["menu"]);
    }
    return $menu;
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