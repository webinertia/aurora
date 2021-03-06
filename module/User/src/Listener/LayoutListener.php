<?php

declare(strict_types=1);

namespace User\Listener;

use App\Filter\FqcnToControllerName;
use App\Filter\FqcnToModuleName;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\FilterChain;
use Laminas\Filter\FilterInterface;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;

class LayoutListener extends AbstractListenerAggregate
{
    /** @var TemplateMapResolver */
    private $templateMapResolver;
    /** @var FilterInterface */
    private $filter;
    /** @var string */
    protected $targetController = 'AccountController';
    /** @var string */
    protected $targetModule = 'User';
    /** @var FqcnToControllerName $cnameFilter */
    protected $cnameFilter;
    /** @var FqcnToModuleName $moduleNameFilter */
    protected $moduleNameFilter;
    /** @return void */
    public function __construct(TemplateMapResolver $templateMapResolver)
    {
        $this->templateMapResolver = $templateMapResolver;
        $this->filter              = (new FilterChain())->attach(new CamelCaseToDash())
                                                        ->attach(new StringToLower());
        $this->cnameFilter         = new FqcnToControllerName();
        $this->moduleNameFilter    = new FqcnToModuleName();
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setLayout']);
    }

    public function setLayout(MvcEvent $event): void
    {
        // Get and check the route match object
        $routeMatch = $event->getRouteMatch();
        if (! $routeMatch) {
            return;
        }

        // Get and check the parameter for current controller
        $controller = $routeMatch->getParam('controller');
        if (! $controller) {
            return;
        }

        // Extract module name
        $module         = $this->moduleNameFilter->filter($controller);
        $controllerName = $this->cnameFilter->filter($controller);
        if ($controllerName !== $this->targetController || $module !== $this->targetModule) {
            return;
        }
        // Convert the module name from camel case to a lower string with dashes
        $name = $this->filter->filter($module) . 'layout/account-dashboard';
        // Has the resolver an entry / layout with the given name?
        if (! $this->templateMapResolver->has($name)) {
            return;
        }

        // Get root view model
        $layoutViewModel = $event->getViewModel();
        // Rendering without layout?
        if ($layoutViewModel->terminate()) {
            return;
        }

        // Change template
        $layoutViewModel->setTemplate($name);
    }
}
