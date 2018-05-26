<?php
/*
* помощник view для вывода навигационных элементов
* генерирует список номеров страниц из объекта Pagination 
*/

namespace Mf\Navigation\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\PaginationControl;
use Zend\Paginator;
use Exception;

/**
 * помощник - вывода номеров страниц
 */
class Pagination extends AbstractHelper 
{
    protected $_default=[
        "simba"=>[/*старый вид*/
            "ScrollingStyle"=>"sliding",        //Тип скроллинга, см. документацию ZF3
            "RouteNamePageStart" =>"home",      //Имя основного маршрута для генерации ссылок
            "RouteNamePages"=>"home",           //Имя маршрута с номерами страниц
            "RouteValues" => [],                //Значения параметров в маршрут в виде ассоциативного массива
            "RoutePageParameterName"=>"page",   //имя параметра с номером страницы в маршруте
            "tpl"=>null,                        //внешний сценарий генерации номеров страниц
        ],
        "bootstrap4"=>[ /*параметры стандарта bootstrap4*/
            "ScrollingStyle"=>"sliding",
            "RouteNamePageStart" =>"home",
            "RouteNamePages"=>"home",
            "RouteValues" => [],
            "RoutePageParameterName"=>"page",
            "tpl"=>null,
        ],
    ];

/*
* 
* 
*/
public function __invoke(Paginator\Paginator $paginator ,array $options=[])
{
    $options=$this->normalizeOptions($options);
    $view=$this->getView();
    $p=$this->getView()->plugin(__NAMESPACE__.'\\'.ucwords($options["type"]).'\Pagination');
    $pages = get_object_vars($paginator->getPages($options["ScrollingStyle"]));

    return $p($pages,$options);
}


    


/*
* нормализация опций, возвращаются опции дополненные значениями по умолчанию 
* возвращает нормализованный массив опций
*/
public function normalizeOptions(array $options)
{
    $type=array_keys($options);
    
    if (empty($type)){
        $type="simba";
        $options=$this->_default[$type];
    } else {
        $type=strtolower($type[0]);
        if (!isset($this->_default[$type])) {
            throw new  Exception("Не допустимый тип навигации $type");
        }
        if (!isset($options[$type]) || !is_array($options[$type])) {
            throw new  Exception("Не допустимые параметры для генерации навигации");
        }

        $options=array_replace_recursive($this->_default[$type],$options[$type]);
    }
    
    $options["type"]=$type;
    return $options;
}
}