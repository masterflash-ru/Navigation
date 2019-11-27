<?php
namespace Mf\Navigation\Service\Admin\Zform\Plugin;

use Psr\Container\ContainerInterface;

/*

*/

class FactoryToolBarInit
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$config=$container->get('config');
    return new $requestedName($config);
}
}

