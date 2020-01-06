<?php
/**
 */

namespace Mf\Navigation\Service;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

//use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Constructed factory to set pages during construction.
 */
class NavigationFactory implements FactoryInterface
{
    /**
     * Create and return a navigation helper instance. (v3)
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return Navigation
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $helper = new $requestedName();
        $helper->setServiceLocator($container);
        return $helper;
    }
}
