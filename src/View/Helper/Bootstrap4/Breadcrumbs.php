<?php
/**
 * 
 */

namespace Mf\Navigation\View\Helper\Bootstrap4;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\Navigation\Page\Mvc;
use Zend\View\Helper\Navigation\Breadcrumbs as ZendBreadcrumbs;

/**
 * 
 */
class Breadcrumbs extends ZendBreadcrumbs
{
    /**
     * CSS class to use for the ol element
     *
     * @var string
     */
    protected $olClass = 'breadcrumb';


    /**
     * Renders breadcrumbs by chaining 'a' elements with the separator
     * registered in the helper.
     *
     * @param  AbstractContainer $container [optional] container to render. Default is
     *                                      to render the container registered in the helper.
     * @return string
     */
    public function renderStraight($container = null)
    {
        $this->parseContainer($container);
        if (null === $container) {
            $container = $this->getContainer();
        }

        // find deepest active
        if (! $active = $this->findActive($container)) {
            return '';
        }

        $active = $active['page'];

        // смотрим последнюю страницу, генерируем ссылку или просто текст
        if ($this->getLinkLast()) {
            $html ='<li class="breadcrumb-item">'. $this->htmlify($active).'</li>'.  PHP_EOL;
        } else {
            /** @var \Zend\View\Helper\EscapeHtml $escaper */
            $escaper = $this->view->plugin('escapeHtml');
            $html    = '<li class="breadcrumb-item">'.$escaper(
                $this->translate($active->getLabel(), $active->getTextDomain())
            ).'</li>'.  PHP_EOL;
        }

        // walk back to root
        while ($parent = $active->getParent()) {
            if ($parent instanceof AbstractPage) {
                // prepend crumb to html
                $html = '<li class="breadcrumb-item">'.$this->htmlify($parent).'</li>'.  PHP_EOL.$html.  PHP_EOL;
            }

            if ($parent === $container) {
                // at the root of the given container
                break;
            }

            $active = $parent;
        }

        return strlen($html) ? $this->getIndent() . '<ol class="breadcrumb">'.PHP_EOL.$html.PHP_EOL.'</ol>' : '';
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty.
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param  AbstractPage $page page to generate HTML for
     * @param  bool $escapeLabel Whether or not to escape the label
     * @param  bool $isChild Whether or not to add the page class to the list item
     * @return string
     */
    public function htmlify11(AbstractPage $page, $escapeLabel = true, $isChild = false)
    {
        // get attribs for element
        $attribs = [
            'id'    => $page->getId(),
            'title' => $this->translate($page->getTitle(), $page->getTextDomain()),
        ];


        $class[] = $page->getClass();

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        if (count($class) > 0) {
            $attribs['class'] = implode(' ', $class);
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>';
        $label = $this->translate($page->getLabel(), $page->getTextDomain());

        if ($escapeLabel === true) {
            /** @var \Zend\View\Helper\EscapeHtml $escaper */
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }

        $html .= '</' . $element . '>';

        return $html;
    }
}
