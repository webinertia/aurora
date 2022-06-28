<?php

declare(strict_types=1);

namespace User\Form\Factory;

use App\Model\Settings;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\UserForm;
use User\Service\UserInterface;

final class UserFormFactory implements FactoryInterface
{
    protected const IDENTITY = 'userName';
    /**
     * @param string $requestedName
     * @param array $options
     * */
    public function __invoke(ContainerInterface $container, $requestedName, $options = []): UserForm
    {
        return new $requestedName(
            $container->get(AclInterface::class),
            $container->get(UserInterface::class),
            $container->get(Settings::class),
            $options
        );
    }
}
