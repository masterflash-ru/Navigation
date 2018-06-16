<?php
/*
*/

namespace Mf\Navigation\View\Helper\Simba;

use Zend\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{
    
public function __invoke(array $pages, array $options=[])
{

    $url_prev="";
    $url_next="";
    $view=$this->getView();
    
    $pages_urls=[];
    $QueryGetValues="";
    
    if (!empty($options["QueryGetValues"])){
        $QueryGetValues="?".http_build_query($options["QueryGetValues"]);
    }

    
    if ($pages["pageCount"]){
        //<!-- Ссылка на предыдущую страницу -->
        if (isset($pages["previous"])){
            if ($pages["previous"] > 1) {
                $url_prev="<a href='".$view->url($options["RouteNamePages"],array_merge(array("page"=>$pages["previous"]),$options["RouteValues"])).$QueryGetValues."'>&larr;</a>".PHP_EOL;
            } else {
                $url_prev="<a href=\"".$view->url($options["RouteNamePageStart"],$options["RouteValues"])."\">&larr;</a>".PHP_EOL;
            }
        }
        
        //<!-- Нумерованные ссылки на страницы -->
        foreach ($pages["pagesInRange"] as $pageitem) {
            if ($pageitem != $pages["current"]) {
                if ($pageitem!=1) {
                    $url=$view->url($options["RouteNamePages"],array_merge(array("page"=>$pageitem),$options["RouteValues"])).$QueryGetValues;
                } else {
                    $url=$view->url($options["RouteNamePageStart"],$options["RouteValues"]).$QueryGetValues;
                }
                
                $pages_urls[]="<a href=\"".$url."\">$pageitem</a>".PHP_EOL;
            } else {
                $pages_urls[]="<span>$pageitem</span>".PHP_EOL;
            }
        }
        //<!-- Ссылка на следующую страницу -->
        if (isset($pages["next"])) {
            $url_next="<a href=\"".$view->url($options["RouteNamePages"],array_merge(array("page"=>$pages["next"]),$options["RouteValues"])).$QueryGetValues."\">&rarr;</a>".PHP_EOL;
        }
    }
    return $url_prev.' '.implode(' ',$pages_urls).' '.$url_next;
}
}
