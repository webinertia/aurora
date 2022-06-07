<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\AdminControllerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use User\Model\Users;
use User\Permissions\PermissionsManager;
use Webinertia\ModelManager\ModelManager;

class AdminListener extends AbstractListenerAggregate
{
    /** @var PermissionsManager $acl */
    protected $acl;
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var ModelManager $modelManager */
    protected $modelManager;
    /** @var TemplateMapResolver */
    private $templateMapResolver;
    /** @return void */
    public function __construct(
        PermissionsManager $acl,
        AuthenticationService $authService,
        ModelManager $modelManager,
        TemplateMapResolver $templateMapResolver
    ) {
        $this->acl                 = $acl;
        $this->authService         = $authService;
        $this->modelManager        = $modelManager;
        $this->templateMapResolver = $templateMapResolver;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'authorize']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setAdminLayout']);
    }

    public function authorize(MvcEvent $event)
    {
        // Get and check the route match object
        $routeMatch        = $event->getRouteMatch();
        $controllerManager = $event->getApplication()->getServiceManager()->get(ControllerManager::class);
        // Get and check the parameter for current controller
        $controller = $routeMatch->getParam('controller');
        $controller = $controllerManager->get($controller);
        if (! $controller instanceof AdminControllerInterface) {
            return;
        }
        $user = $this->modelManager->get(Users::class);
        switch ($this->authService->hasIdentity()) {
            case true:
                $user = $user->fetchColumns(
                    'userName',
                    $this->authService->getIdentity(),
                    $user->getContextColumns()
                );
                break;
            case false:
            default:
                $user->exchangeArray($user->fetchGuestContext());
                break;
        }
        if (! $this->acl->isAllowed($user, 'admin', 'admin.access')) {
            $controller->flashMessenger()->addErrorMessage('The requested action was forbidden');
            $controller->redirect()->toRoute('home');
        }
    }

    public function setAdminLayout(MvcEvent $event): void
    {
         // Get and check the route match object
        $routeMatch        = $event->getRouteMatch();
        $controllerManager = $event->getApplication()->getServiceManager()->get(ControllerManager::class);
        if (! $routeMatch) {
             return;
        }
         // Get and check the parameter for current controller
        $controller = $routeMatch->getParam('controller');
        $controller = $controllerManager->get($controller);
        $name       = 'layout/admin';
        // we this is not an admin controller or if we have already got the layout return
        if (! $controller instanceof AdminControllerInterface || $this->templateMapResolver->has($name)) {
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