<?php
namespace Mf\Navigation\Service\Admin\Zform\Plugin;

use Interop\Container\ContainerInterface;

/*

*/

class FactoryMenuNames
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$config=$container->get('config');
    if (!isset($config["menu"])){
        
    }
    return new $requestedName($config["menu"]);
}
}

