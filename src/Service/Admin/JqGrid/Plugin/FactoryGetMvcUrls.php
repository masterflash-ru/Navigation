<?php
namespace Mf\Navigation\Service\Admin\JqGrid\Plugin;

use Psr\Container\ContainerInterface;
use Zend\EventManager\EventManager;

/*

*/

class FactoryGetMvcUrls
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$SharedEventManager=$container->get('SharedEventManager');
	$SharedEventManager=new EventManager($SharedEventManager);
	$SharedEventManager->addIdentifiers(["simba.admin"]);
    $controllers_descriptions=$SharedEventManager->trigger("GetMvc",NULL,["category"=>"frontend"]);

    return new $requestedName($controllers_descriptions);
}
}

