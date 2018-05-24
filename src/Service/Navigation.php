<?php
/**
 * Прокси для генерации Bootstrap4
 */

namespace Mf\Navigation\Service;

use Zend\View\Helper\Navigation as ZendNavigation;

/**
 * Navigation
 */
class Navigation extends ZendNavigation
{
    /**
     * Default proxy to use in {@link render()}
     *
     * @var string
     */
    protected $defaultProxy = 'menu';

    /**
     * Default set of helpers to inject into the plugin manager
     *
     * @var array
     */
    protected $defaultPluginManagerHelpers
        = [
            'menu'        => Menu::class,
        ];

    /**
     * Retrieve plugin loader for navigation helpers
     *
     * Lazy-loads an instance of Navigation\HelperLoader if none currently
     * registered.
     *
     * @return \Zend\View\Helper\Navigation\PluginManager
     */
    public function getPluginManager(): ZendNavigation\PluginManager
    {
        $pm = parent::getPluginManager();
        foreach ($this->defaultPluginManagerHelpers as $name => $invokableClass) {
            $pm->setAllowOverride(true);
            $pm->setInvokableClass($name, $invokableClass);
        }

        return $pm;
    }


}
