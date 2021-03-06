<?php

declare(strict_types=1);

namespace App\Controller;

use App\Log\LoggerAwareInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

interface AdminControllerInterface extends ResourceInterface, LoggerAwareInterface
{
}
