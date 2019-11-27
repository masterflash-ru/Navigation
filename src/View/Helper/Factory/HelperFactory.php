<?php
namespace Mf\Navigation\View\Helper\Factory;

use Psr\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * универсальная фабрика для меню/крошек .....
 * 
 */
class HelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
     $cache = $container->get('DefaultSystemCache');
     $connection=$container->get('DefaultSystemDb');
        return new $requestedName($connection,$cache,$container);
    }
}

