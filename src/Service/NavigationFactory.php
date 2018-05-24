<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mf\Navigation\Service;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

//use Zend\ServiceManager\Factory\FactoryInterface;

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
