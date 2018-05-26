<div class="pagination">
<?php
/*
Генератор нумерации строк, на входе уже объект Paginator полностью инициализированный
В качестве опций передается имя маршрута который нужно использовать:

route_name_page - имя маршрута с обработкой номеров страниц
route_name_default - имя маршрута без обработки номеров страниц (переход на первую) 
	- такое используется для исключения дубляжа url

параметр url_parameters - массив который добавляется при генерации ссылок переходом между страницми

*/
	
$url_prev="";
$url_next=""; 

$pages_urls=[];
if (!is_array($this->url_parameters)) $this->url_parameters=[];

if ($this->pageCount)
	{
		//<!-- Ссылка на предыдущую страницу -->
		if (isset($this->previous))
			{
				if ($this->previous>1) 
					{
						$url_prev="<a href='".$this->url($this->route_name_page,array_merge(array("page"=>$this->previous),$this->url_parameters))."'>&larr;</a>";
					}
				else 
					{
						$url_prev="<a href=\"".$this->url($this->route_name_default,$this->url_parameters)."\">&larr;</a>";
					}
			}
					 
		//<!-- Нумерованные ссылки на страницы -->
		foreach ($this->pagesInRange as $pageitem)
			{
				if ($pageitem != $this->current)
					{
						if ($pageitem!=1) 
							{
								$url=$this->url($this->route_name_page,array_merge(array("page"=>$pageitem),$this->url_parameters));
							}
						else 
							{
								$url=$this->url($this->route_name_default,$this->url_parameters);
							}
						$pages_urls[]="<a href=\"".$url."\">$pageitem</a>";
					}
				else
					{
							$pages_urls[]="<span>$pageitem</span>";
					}
			}
					 
	//<!-- Ссылка на следующую страницу -->
		if (isset($this->next))
					{
						$url_next="<a href=\"".$this->url($this->route_name_page,array_merge(array("page"=>$this->next),$this->url_parameters))."\">&rarr;</a>";
					}
	}
echo $url_prev.' '.implode(' ',$pages_urls).' '.$url_next;
?>
</div>