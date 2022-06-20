<?php

declare(strict_types=1);

namespace User\Service;

use Laminas\Permissions\Acl\ProprietaryInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

interface UserInterface extends ProprietaryInterface, ResourceInterface, RoleInterface
{
}
