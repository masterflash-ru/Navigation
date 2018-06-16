<?php

namespace Mf\Navigation\View\Helper\Bootstrap4;

use Zend\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{

public function __invoke(array $pages, array $options=[])
{
    $view=$this->getView();
    $pages_urls=[];

    if ($pages["pageCount"]){
        //<!-- Ссылка на предыдущую страницу -->

        if (isset($pages["previous"]) && $pages["previous"]>1) {
                $pages_urls[]="<li class=\"page-item\"><a class=\"page-link\" href='".$view->url($options["RouteNamePages"],$options["RouteValues"],array_merge(array("page"=>$pages["previous"])))."'>&laquo;</a></li>".PHP_EOL;
            }else {
                $pages_urls[]="<li class=\"disabled page-item\"><a class=\"page-link\" href=\"".$view->url($options["RouteNamePageStart"],$options["RouteValues"])."\">&laquo;</a></li>".PHP_EOL;
        }

        //<!-- Нумерованные ссылки на страницы -->
        foreach ($pages["pagesInRange"] as $pageitem){
            $url=$view->url($options["RouteNamePages"],array_merge(array("page"=>$pageitem),$options["RouteValues"]));
            if ($pageitem != $pages["current"]){
                if ($pageitem==1) {
                    $url=$view->url($options["RouteNamePageStart"],$options["RouteValues"]);
                }
                $pages_urls[]="<li class=\"page-item\"><a class=\"page-link\" href=\"".$url."\">$pageitem</a></li>".PHP_EOL;
            } else {
                $pages_urls[]="<li class=\"active page-item\"><a class=\"page-link\" href=\"".$url."\">$pageitem</a></li>".PHP_EOL;
            }
        }

        //<!-- Ссылка на следующую страницу -->
        if (isset($pages["next"])){
                $pages_urls[]="<li><a class=\"page-link\" href=\"".$view->url($options["RouteNamePages"],array_merge(array("page"=>$pages["next"]),$options["RouteValues"]))."\">&raquo;</a>".PHP_EOL;
        } else {
                $pages_urls[]="<li class=\"disabled page-item\"><a class=\"page-link\" href=\"#\">&raquo;</a></li>".PHP_EOL;
        }
    }
    return  '<ul class="pagination justify-content-center pagination-sm">'.PHP_EOL.implode(' ',$pages_urls).PHP_EOL.'</ul>'.PHP_EOL;

    }
    
}
