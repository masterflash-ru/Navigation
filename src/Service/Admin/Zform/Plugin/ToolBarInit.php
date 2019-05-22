<?php
namespace Mf\Navigation\Service\Admin\Zform\Plugin;

use Admin\Service\Zform\Plugin\AbstractPlugin;

class ToolBarInit extends AbstractPlugin
{
	protected $config;

    public function __construct($config) 
    {
		$this->config=$config;
    }
    


 public function read()
 {
    if (!isset($this->config["menu"])){
        throw new  \Exception("Нет конфигурации меню в приложении, смотрите пакет masterflash-ru/navigation");
    }

     return ["locale"=>$this->config["locale_default"],"sysname"=>key($this->config["menu"])];
 }


}