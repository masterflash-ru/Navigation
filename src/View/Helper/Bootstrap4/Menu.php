<?php
/**
 * 
 */

namespace Mf\Navigation\View\Helper\Bootstrap4;

use RecursiveIteratorIterator;
use Laminas\Navigation\AbstractContainer;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\Navigation\Page\Mvc;
use Laminas\View\Helper\Navigation\Menu as LaminasMenu;

/**
 * 
 */
class Menu extends LaminasMenu
{
    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $ulClass = 'nav';

    /**
     * Renders a normal menu (called from {@link renderMenu()}).
     *
     * @param  AbstractContainer $container container to render
     * @param  string $ulClass CSS class for first UL
     * @param  string $indent initial indentation
     * @param  int|null $minDepth minimum depth
     * @param  int|null $maxDepth maximum depth
     * @param  bool $onlyActive render only active branch?
     * @param  bool $escapeLabels Whether or not to escape the labels
     * @param  bool $addClassToListItem Whether or not page class applied to <li> element
     * @param  string $liActiveClass CSS class for active LI
     * @return string
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    )
    
    {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);

        /* @var $escaper \Laminas\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            /** @var Mvc $foundPage */
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );

        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        /** @var Mvc $page */
        foreach ($iterator as $page) {
            //наши дополнительные опции
            $_options=$page->get("options");
            
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            }

            if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible)
                            || is_int($maxDepth) && $foundDepth + 1 > $maxDepth
                        ) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }
                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('    ', $depth + 1);
            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth == 0) {
                    $ulClass = ' class="' . $escaper($ulClass) . '"';
                } else {
                    $ulClass = '';
                }

                if ($depth === 0) {
                    $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
                } else {
                    $html .= $myIndent . '<div class="dropdown-menu">' . PHP_EOL;
                }
            }

            // render li tag and page
            $liClasses = ['nav-item'];
            if (!empty($_options["li_class"])){
                $li_class[]=$_options["li_class"];
            }

            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }

            if ($page->hasPages()) {
                $liClasses[] = 'dropdown';
            }

            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';

            if ($depth < $prevDepth) {
                $html .= '       ' . $myIndent . '</div>' . PHP_EOL;

                if ($depth === 0)
                {
                    $html .= '    ' . '</li>' . PHP_EOL;
                }
            }

            $html .= $myIndent
                . ($depth === 0 ? '    <li' . $liClass .'>' : '')
                . $myIndent . $this->htmlify($page, $escapeLabels, $depth > 0,$_options)
                . PHP_EOL;


            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i - 1);
                if ($i==1){
                    $html .= $myIndent . '    </li>' . PHP_EOL . $myIndent . '</ul>' . PHP_EOL;
                } else {
                    $html .= $myIndent . '    </div>'. PHP_EOL ;
                }
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
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
    public function htmlify(AbstractPage $page, $escapeLabel = true, $isChild = false,$options=[])
    {
        // get attribs for element
        $attribs = [
            'id'    => $page->getId(),
            'title' => $this->translate($page->getTitle(), $page->getTextDomain()),
        ];


        $class[] = $page->getClass();

        if (!$isChild && $page->hasPages()) {
            $attribs['data-toggle'] = 'dropdown';
            $attribs['role'] = 'button';
            $class[] = 'dropdown-toggle';
        }
        // does page have a href?
        $href = $page->getHref();

        if ($isChild) {
            if (!empty($options["header"])){
                $class[]='dropdown-header';
                $href=null;
            } else {
                $class[] = 'dropdown-item';
            }
        } else {
            $class[] = 'nav-link';
        }
        
        if (!empty($options["a_class"])){
            $class[]=$options["a_class"];
        }


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
            /** @var \Laminas\View\Helper\EscapeHtml $escaper */
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }

        $html .= '</' . $element . '>';

        return $html;
    }
}
