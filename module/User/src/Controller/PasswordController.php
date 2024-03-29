<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Log\LogEvent;
use App\Service\Email;
use DateTime;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Filter\RegistrationHash;
use User\Form\ResetPassword;
use User\Service\UserService;
use User\Service\UserServiceInterface;

use function sprintf;

final class PasswordController extends AbstractAppController
{
    /** @var string $resourceId */
    protected $resourceId = 'account';
    public function resetAction(): ViewModel
    {
        try {
            $appSettings = $this->getService('config')['app_settings'];
            $step        = $this->params('step', 'zero');
            $dateTime    = new DateTime('NOW');
            $options     = ['db' => $this->userService, 'enableCaptcha' => $appSettings['security']['enable_captcha']];
            $form        = new ResetPassword(null, $options);
            $this->view->setVariable('showForm', true);
            switch ($step) {
                case 'submit-email':
                    $startTime = $dateTime->format($appSettings['server']['time_format']);
                    $formData  = ['resetTimeStamp' => $startTime, 'step' => 'two'];
                    $form->remove('password');
                    $form->remove('conf_password');
                    $form->setAttribute('action', '/user/password/reset/send-email');
                    $form->setData($formData);
                    break;
                case 'send-email':
                    if ($this->request->isPost()) {
                        $this->view->setVariable('showForm', false);
                        $form->setValidationGroup(['email', 'resetTimeStamp']);
                        $post = $this->request->getPost();
                        $form->setData($post);
                        if ($form->isValid()) {
                            $data = $form->getData();
                        }
                        /** @var UserService $user */
                        $user = $this->userService->fetchByColumn('email', $post['email']);
                        $user->setFilterPassword(false);
                        if ($user instanceof UserServiceInterface) {
                            $filter               = new RegistrationHash();
                            $hash                 = $filter->filter(
                                ['email' => $post['email'], 'timestamp' => $post['resetTimeStamp']]
                            );
                            $user->resetTimeStamp = $post['resetTimeStamp'];
                            $user->resetHash      = $hash;
                            if ($user->save($user->toArray())) {
                                try {
                                    $this->email()->sendMessage($post['email'], Email::RESET_PASSWORD, $hash);
                                } catch (Throwable $th) {
                                    $this->getEventManager()->trigger(
                                        LogEvent::ERROR,
                                        'log_password_update_reset_email_failure'
                                    );
                                }
                                $this->getEventManager()->trigger(
                                    LogEvent::INFO,
                                    'log_password_update_request'
                                );
                                // condition is when you have just submitted your email to be sent a link to reset
                                // @codingStandardsIgnoreStart
                                $this->flashMessenger()->addInfoMessage(
                                    $this->getTranslator()->translate('password_reset_link_sent')
                                );
                                // @codingStandardsIgnoreEnd
                                // redirect
                                $this->redirect()->toRoute('home');
                            } else {
                                throw new RuntimeException(
                                    $this->getTranslator()->translate('password_update_failure')
                                );
                            }
                        }
                    }
                    break;
                case 'reset-password':
                    $token = $this->request->getQuery('token');
                    /** @var UserService $user */
                    $user = $this->userService->fetchByColumn('resetHash', $token);
                    if (! $user instanceof UserServiceInterface) {
                        throw new RuntimeException('User not found');
                    }
                    $this->getEventManager()->trigger(
                        LogEvent::INFO,
                        sprintf(
                            $this->getTranslator()->translate('log_failed_password_reset_ip'),
                            $this->getRequest()->getServer('REMOTE_ADDR')
                        )
                    );
                    // @codingStandardsIgnoreStart
                    $this->flashMessenger()->addErrorMessage(
                        $this->getTranslator()->translate('password_reset_token_expired')
                    );
                    // @codingStandardsIgnoreEnd
                    // this needs to redirect to a contact page.
                    $this->redirect()->toRoute('home');
                    if (! $this->request->isPost()) {
                        $this->view->setVariable('showForm', true);
                        $form->remove('email');
                        $form->setAttribute('action', '/user/password/reset/reset-password?token=' . $token);
                        $startTime = DateTime::createFromFormat(
                            $this->appSettings['server']['time_format'],
                            $user->resetTimeStamp
                        );
                        $limit     = DateTime::createFromFormat(
                            $this->appSettings['server']['time_format'],
                            $dateTime->format($this->appSettings['server']['time_format'])
                        );
                        $interval  = $startTime->diff($limit);
                        if ($interval->d > 0) {
                            $this->flashMessenger()->addErrorMessage(
                                $this->getTranslator()->translate('password_reset_link_expired')
                            );
                            return $this->redirect()->toRoute(
                                'password',
                                ['action' => 'reset', 'step' => 'submit-email']
                            );
                        }
                    } else {
                        $form->setInputFilter($form->addInputFilter());
                        $form->remove('email');
                        $form->setValidationGroup(['password', 'conf_password']);
                        $post = $this->request->getPost();
                        $form->setData($post);
                        if ($form->isValid()) {
                            $data                 = $form->getData();
                            $user->password       = $data['password'];
                            $user->resetTimeStamp = null;
                            $user->resetHash      = null;
                            $user->setFilterPassword(true);
                            if ($this->userService->save($user->toArray(), $user->id)) {
                                $this->flashMessenger()->addSuccessMessage(
                                    'Your password has been succesfully updated'
                                );
                                return $this->redirect()->toRoute('user', ['action' => 'login']);
                            } else {
                                $this->flashMessenger()->addErrorMessage(
                                    'Your password was not updated due to an error processing your request'
                                );
                                return $this->redirect()->toRoute('home');
                            }
                        }
                    }
                    break;
            }
            $this->view->setVariable('form', $form);
        } catch (RuntimeException $e) {
            $this->getEventManager()->trigger(
                LogEvent::ERROR,
                $this->getTranslator()->translate('log_password_update_failure')
                . 'Exception Info: '
                . $e->getFile()
                . ': '
                . $e->getLine()
                . ' - '
                . $e->getMessage()
            );
        }
        return $this->view;
    }
}
