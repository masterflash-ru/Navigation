<div class="text-center">
<?php
/*
Генератор нумерации строк, на входе уже объект Paginator полностью инициализированный
В качестве опций передается имя маршрута который нужно использовать:

route_name_page - имя маршрута с обработкой номеров страниц
route_name_default - имя маршрута без обработки номеров страниц (переход на первую) 
	- такое используется для исключения дубляжа url

параметр url_parameters - массив который добавляется при генерации ссылок переходом между страницми

*/
$pages_urls=[];
if (!is_array($this->url_parameters)) $this->url_parameters=[];

if ($this->pageCount){
	//<!-- Ссылка на предыдущую страницу -->

	if (isset($this->previous) && $this->previous>1) {
			$pages_urls[]="<li class=\"page-item\"><a class=\"page-link\" href='".$this->url($this->route_name_page,$this->url_parameters,array_merge(array("page"=>$this->previous)))."'>&laquo;</a></li>";
		}else {
			$pages_urls[]="<li class=\"disabled page-item\"><a class=\"page-link\" href=\"".$this->url($this->route_name_default,$this->url_parameters)."\">&laquo;</a></li>";
	}

	//<!-- Нумерованные ссылки на страницы -->
	foreach ($this->pagesInRange as $pageitem){
		$url=$this->url($this->route_name_page,array_merge(array("page"=>$pageitem),$this->url_parameters));
		if ($pageitem != $this->current){
			if ($pageitem==1) {
				$url=$this->url($this->route_name_default,$this->url_parameters);
			}
			$pages_urls[]="<li class=\"page-item\"><a class=\"page-link\" href=\"".$url."\">$pageitem</a></li>";
		} else {
			$pages_urls[]="<li class=\"active page-item\"><a class=\"page-link\" href=\"".$url."\">$pageitem</a></li>";
		}
	}
					 
	//<!-- Ссылка на следующую страницу -->
	if (isset($this->next)){
			$pages_urls[]="<li><a class=\"page-link\" href=\"".$this->url($this->route_name_page,array_merge(array("page"=>$this->next),$this->url_parameters))."\">&raquo;</a>";
	} else {
			$pages_urls[]="<li class=\"disabled page-item\"><a class=\"page-link\" href=\"#\">&raquo;</a></li>";
	}
}
echo '<ul class="pagination justify-content-center pagination-sm">'.implode(' ',$pages_urls).'</ul>';
?>
</div>
