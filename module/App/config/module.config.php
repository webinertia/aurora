<?php

declare(strict_types=1);

namespace App;

use Laminas\Mvc\I18n\Router\TranslatorAwareTreeRouteStack;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function dirname;

return [
    'base_dir'        => dirname(__DIR__, 3),
    'db'              => [
        'sessions_table_name' => 'sessions',
        'log_table_name'      => 'log',
        'theme_table_name'    => 'theme',
    ],
    'router'          => [
        'router_class' => TranslatorAwareTreeRouteStack::class,
        'routes'       => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/test',
                    'defaults' => [
                        'controller' => TestController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'site' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contact' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site/contact[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'contact',
                    ],
                ],
            ],
            'admin' => [
                'type' => Placeholder::class,
                'may_terminate' => true,
                'child_routes'  => [
                    'dashboard' => [
                        'may_terminate' => true,
                        'type'          => Literal::class,
                        'options' => [
                            'route'    => '/admin',
                            'defaults' => [
                                'controller' => AdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin.add.setting' => [
                        'may_terminate' => true,
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/admin/addsetting',
                            'defaults' => [
                                'controller' => AdminController::class,
                                'action'     => 'addsetting',
                            ],
                        ],
                    ],
                    // need to add route for image uploads
                    'admin.add.setting' => [
                        'may_terminate' => true,
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '/admin/upload[/:module[/:type]]',
                            'constraints' => [
                                'module' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'type'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => AdminController::class,
                                'action'     => 'upload',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'model_manager'   => [
        'factories' => [
            Model\Settings::class => Model\Factory\SettingsFactory::class,
            Model\Theme::class    => Model\Factory\ThemeFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\Email::class                        => Service\Factory\EmailFactory::class,
            Laminas\Session\SessionManager::class       => Laminas\Session\Service\SessionManagerFactory::class,
            Laminas\Session\Config\SessionConfig::class => Laminas\Session\Service\SessionConfigFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\AdminController::class => Controller\Factory\AppControllerFactory::class,
            Controller\IndexController::class => Controller\Factory\AppControllerFactory::class,
            Controller\TestController::class  => Controller\Factory\AppControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'factories' => [
            Form\ContactForm::class               => Form\Factory\ContactFormFactory::class,
            Form\Fieldset\SecurityFieldset::class => Form\Fieldset\Factory\SecurityFieldsetFactory::class,
        ],
    ],
    'filters'         => [
        'invokables' => [
            Filter\FqcnToControllerName::class => InvokableFactory::class,
            Filter\FqcnToModuleName::class     => InvokableFactory::class,
        ],
    ],
    'navigation'      => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
                'class' => 'nav-link',
                'order' => '-10',
            ],
            [
                'label' => 'Contact Us',
                'route' => 'contact',
                'class' => 'nav-link',
                'order' => '20',
            ],
            [
                'label'     => 'Admin',
                'uri'       => '/admin',
                'class'     => 'nav-link',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
            ],
        ],
        'admin' => [
            [
                'label'     => 'Home',
                'uri'       => '/',
                'iconClass' => 'mdi mdi-home text-success',
                'order'     => '-100',
            ],
            [
                'label'     => 'Dashboard',
                'uri'       => '/admin',
                'iconClass' => 'mdi mdi-speedometer text-success',
                'order'     => '-99',
            ],
            [
                'label'     => 'Manage Settings',
                'uri'       => '/admin/manage-settings',
                'iconClass' => 'mdi mdi-wrench text-danger',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
            ],
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'bootstrapform'           => View\Helper\BootstrapForm::class,
            'bootstrapForm'           => View\Helper\BootstrapForm::class,
            'bootstrapformcollection' => View\Helper\BootstrapFormCollection::class,
            'bootstrapFormCollection' => View\Helper\BootstrapFormCollection::class,
            'bootstrapformrow'        => View\Helper\BootstrapFormRow::class,
            'bootstrapFormRow'        => View\Helper\BootstrapFormRow::class,
            'iconifiedcontrol'        => View\Helper\IconifiedControl::class,
            'iconifiedControl'        => View\Helper\IconifiedControl::class,
        ],
        'factories' => [
            View\Helper\BootstrapForm::class           => InvokableFactory::class,
            View\Helper\BootstrapFormCollection::class => InvokableFactory::class,
            View\Helper\BootstrapFormRow::class        => InvokableFactory::class,
            View\Helper\IconifiedControl::class        => View\Helper\Service\IconifiedControlFactory::class,
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'display_forbidden_notice' => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'forbidden_template'       => 'error/403',
        'template_map'             => [],
    ],
    'upload_manager'  => [
        'App' => [
            'upload_path' => '/images',
        ],
    ],
    'translator'      => [
        'locale' => [
            //'en_US'
        ],
        'translation_file_patterns' => [
            [
                'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
                'filename' => 'en-US.php',
                'base_dir' => __DIR__ . '/languages',
                'pattern'  => '%s.php',
            ],
//             [
//                 'type'        => Laminas\I18n\Translator\Loader\PhpArray::class,
//                 'base_dir'    => __DIR__ . '/languages',
//                 'pattern'     => 'user-%s.php',
//                 'text_domain' => 'user',
//             ],

        ],
        'translation_files' => [
            [
                'type'        => 'PhpArray',
                'filename'    => dirname(__DIR__, 3) . '/languages/en-US.php',
                'locale'      => 'en-US',
                'text_domain' => 'default',
            ],
//             [
//                 'type' => 'PhpArray',
//                 'filename' => dirname(__DIR__, 3) . '/languages/user-en-US.php',
//                 'text_domain' => 'user',
//                 'locale' => 'en-US',
//             ],
            [
                'type'        => 'PhpArray',
                'filename'    => dirname(__DIR__, 3) . '/languages/es-MX.php',
                'text_domain' => 'default',
                'locale'      => 'es-MX',
            ],
//             [
//                 'type' => 'PhpArray',
//                 'filename' => dirname(__DIR__, 3) . '/languages/user-es-MX.php',
//                 'text_domain' => 'user',
//                 'locale' => 'es-MX',
//             ],
        ],
    ],
];