<?php


namespace Mf\Navigation\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;
use Zend\Escaper\Escaper;


class GetMvcUrls extends AbstractPlugin
{

    protected $controllers_descriptions;
    
    public function __construct($controllers_descriptions)
    {
        $this->controllers_descriptions=$controllers_descriptions;
    }

    /**
    * асинхронно выдает список MVC адресов для генерации списка в админке
    * использует значение из toolbar поле locale, 
    * все поля toolbar парадеются GET параметром сюда, если они конечно есть
    */
    public function ajaxRead(array $toolbarData=[])
    {
        $rez[""]="";
        $escape=new Escaper("utf-8");
        foreach ($this->controllers_descriptions as $name=>$desc){
            //внутри контроллера
            if (is_array($desc) && !empty($desc)) {
                foreach ($desc as $meta) {
                    $r=[];
                    foreach ($meta["urls"]["mvc"][$toolbarData["locale"]] as $k=>$item){
                        $item=$escape->escapeHtmlAttr($item);
                        $r[$item]=$meta["urls"]["name"][$toolbarData["locale"]][$k];
                    }
                    $rez[$meta["description"]]=$r;
                }
            }
        }
        return $rez;
    }

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * $toolbarData - массив начальных данных из раздела toolbar
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel,array $toolbarData=[])
    {
        $rez[""]="";
        foreach ($this->controllers_descriptions as $name=>$desc){
            //внутри контроллера
            if (is_array($desc)) {
                foreach ($desc as $meta) {//цикл по отдельным модулям
                    foreach ($meta["urls"]["mvc"][$toolbarData["locale"]] as $k=>$item){
                        $rez[$item]=$meta["description"].' -> '.$meta["urls"]["name"][$toolbarData["locale"]][$k];
                    }
                    
                }
            }
        }
        $colModel["editoptions"]["value"]=$rez;
        return $colModel;
    }



}