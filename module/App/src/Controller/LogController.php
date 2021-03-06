<?php

/**
 * This is an admin controller since it implements the AdminControllerInterface.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Db\DbGateway\LogGateway;
use App\Log\LogEvent;
use Laminas\View\Model\ViewModel;
use Throwable;
use User\Acl\CheckActionAccessTrait;
use User\Acl\ResourceAwareTrait;

final class LogController extends AbstractAppController implements AdminControllerInterface
{
    use CheckActionAccessTrait;
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'logs';
    public function viewAction(): ViewModel
    {
        $headers = $this->response->getHeaders();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->isAllowed()) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $logGateway = $this->getService(LogGateway::class);
        $select     = $logGateway->getSql()->select();
        $select->order(['logId DESC']);
        $resultSet = $logGateway->selectWith($select);
        $check     = $this->acl()->isAllowed($this->identity()->getIdentity(), $this->resourceId, 'delete');
        $this->view->setVariables(
            [
                'data'        => $resultSet,
                'resourceId'  => $this->resourceId,
                'allowDelete' => $this->acl()->isAllowed($this->identity()->getIdentity(), $this->resourceId, 'delete'),
            ]
        );
        return $this->view;
    }

    public function errorAction(): ViewModel
    {
        return $this->view;
    }

    public function deleteAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->isAllowed()) {
            $this->response->setStatusCode(403);
            return $this->view;
        }
        $gateway = $this->getService(LogGateway::class);
        $logId   = $this->params()->fromRoute('id');
        if ($logId !== null) {
            try {
                $gateway->delete(['logId' => $logId]);
            } catch (Throwable $th) {
                $this->getEventManager()->trigger(LogEvent::ERROR, $th->getMessage());
            }
        }
        return $this->view;
    }
}
