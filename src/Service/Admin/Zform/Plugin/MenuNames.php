<?php
namespace Mf\Navigation\Service\Admin\Zform\Plugin;

use Zend\Form\FormInterface;
use Admin\Service\Zform\Plugin\AbstractPlugin;

class MenuNames extends AbstractPlugin
{
	protected $config;

    public function __construct($config) 
    {
		$this->config=$config;
    }
    


 public function rowModel(array $rowModel,FormInterface $form)
 {
        $rez=[];
        foreach ($this->config as $k=>$l){
            $rez[$k]=$l;
        }
        $colModel["editoptions"]["value"]=$rez;
     $form->get($rowModel["name"])->setValueOptions($rez);
 }


}