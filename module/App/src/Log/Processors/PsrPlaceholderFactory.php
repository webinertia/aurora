<?php

declare(strict_types=1);

namespace App\Log\Processors;

use App\Log\Processors\PsrPlaceholder;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserServiceInterface;

final class PsrPlaceholderFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): PsrPlaceholder {
        return new $requestedName($container->get(UserServiceInterface::class));
    }
}
