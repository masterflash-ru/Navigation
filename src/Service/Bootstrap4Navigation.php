<?php
/**
 * Прокси для генерации разных элементов
 */

namespace Mf\Navigation\Service;

use Laminas\View\Helper\Navigation as LaminasNavigation;

use Mf\Navigation\View\Helper\Bootstrap4;

/**
 * Navigation
 */
class Bootstrap4Navigation extends LaminasNavigation
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
            'menu'        => Bootstrap4\Menu::class,
            'breadcrumbs' => Bootstrap4\Breadcrumbs::class,
        ];

    /**
     * Retrieve plugin loader for navigation helpers
     *
     * Lazy-loads an instance of Navigation\HelperLoader if none currently
     * registered.
     *
     * @return \Laminas\View\Helper\Navigation\PluginManager
     */
    public function getPluginManager()
    {
        $pm = parent::getPluginManager();
        foreach ($this->defaultPluginManagerHelpers as $name => $invokableClass) {
            $pm->setAllowOverride(true);
            $pm->setInvokableClass($name, $invokableClass);
        }

        return $pm;
    }


}
