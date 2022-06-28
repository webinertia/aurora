<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use Laminas\Permissions\Acl\ProprietaryInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Stdlib\ArrayObject;

abstract class AbstractGatewayModel extends ArrayObject implements
    ProprietaryInterface,
    ResourceInterface,
    RoleInterface
{
    /** @var string $ownerIdColumn */
    protected $ownerIdColumn;
    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }

    public function getRoleId(): string
    {
        if ($this->offsetExists('role')) {
            return $this->offsetGet('role');
        }
        return null;
    }

    public function getOwnerId(): int
    {
        if (! empty($this->ownerIdColumn) && $this->offsetExists($this->ownerIdColumn)) {
            return (int) $this->offsetGet($this->ownerIdColumn);
        } elseif ($this->offsetExists('userId')) {
            return (int) $this->offsetGet('userId');
        } elseif ($this->offsetExists('user_id')) {
            return (int) $this->offsetGet('user_id');
        }
    }

    public function getResourceId(): string
    {
        return $this->db->getTable();
    }
}