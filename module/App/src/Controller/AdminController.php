<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\SettingsForm;
use App\Form\ThemeSettingsForm;
use App\Log\LogEvent;
use App\Model\Theme;
use Laminas\Config\Exception\RuntimeException as ConfigRuntimeException;
use Laminas\Config\Factory as ConfigFactory;
use Laminas\Config\Writer\PhpArray as ConfigWriter;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Controller\WidgetController;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'admin';

    public function indexAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $memberListWidget = $this->forward()->dispatch(
            WidgetController::class,
            [
                'action' => 'member-list',
                'group'  => 'admin',
            ]
        );
        $this->view->setVariables(['memberListWidget' => $memberListWidget, 'listType' => 'admin']);
        return $this->view;
    }

    public function manageThemesAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $themes  = $this->getService(Theme::class);
        $headers = $this->response->getHeaders();
        if (! $this->isAllowed($themes)) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $form = $this->getService(FormElementManager::class)->get(ThemeSettingsForm::class);
        $form->setAttribute('action', $this->url()->fromRoute('admin/themes/manage'));
        if (! $this->request->isPost()) {
            $form->setData($themes->getConfig());
        }
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $writer = $this->getService(ConfigWriter::class)->get();
                    $writer->setUseBracketArraySyntax(true);
                    $writer->toFile($this->getService(Theme::class)->get()->getConfigPath(), $data);
                    $headers->addHeaderLine('Content-Type', 'application/json');
                    $this->view->setVariables(['success' => true, 'message' => ['message' => 'Settings Saved']]);
                } catch (RuntimeException $e) {
                    $this->getEventManager()->trigger(LogEvent::ERROR, $e->getMessage());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function manageSettingsAction(): ViewModel
    {
        $this->resourceId = 'settings';
        $headers          = $this->response->getHeaders();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->isAllowed($this)) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $form = $this->getService(FormElementManager::class)->get(SettingsForm::class);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin/settings/manage')
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $writer = new ConfigWriter();
                $writer->setUseBracketArraySyntax(true);
                try {
                    $config = $form->getData();
                    $writer->toFile(
                        $this->basePath . '/config/autoload/appsettings.local.php',
                        $form->getData(),
                    );
                } catch (ConfigRuntimeException $e) {
                    $this->getEventManager()->trigger(
                        LogEvent::CRITICAL,
                        $this->getTranslator()->translate('log_settings_file_write_failure')
                        . 'Exception Info: '
                        . $e->getFile() . ' Line#:' . $e->getLine() . ': ' . $e->getMessage()
                    );
                }
                $headers->addHeaderLine('Content-Type', 'application/json');
                $this->getEventManager()->trigger(
                    LogEvent::NOTICE,
                    $this->getTranslator()->translate('log_settings_file_write_success')
                );
                $this->view->setVariables(
                    [
                        'success' => true,
                        'message' => [
                            'message' => $this->getTranslator()->translate('edit_settings_success'),
                        ],
                    ]
                );
            }
        } else {
            try {
                $config = ConfigFactory::fromFile($this->basePath . '/config/autoload/appsettings.local.php');
                if (isset($config['app_settings'])) {
                    $form->setData($config);
                } else {
                    throw new RuntimeException('log_settings_file_read_failure');
                }
            } catch (ConfigRuntimeException $e) {
                $this->getEventManager()->trigger(
                    LogEvent::CRITICAL,
                    $this->getTranslator()->translate($e->getMessage())
                    . 'Exception Info: '
                    . $e->getFile() . ' Line#:' . $e->getLine()
                );
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function addsettingAction(): mixed
    {
        $this->resourceId = 'settings';

        if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
            $this->flashMessenger()->addWarningMessage(
                'Access Denied, your attempt to access this area as been logged'
            );
            $this->response->setStatusCode(403);
        }
        $this->view->setVariable('resourceId', $this->resourceId);
        return $this->view;
    }
}
