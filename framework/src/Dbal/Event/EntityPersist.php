<?php

namespace Web\Framework\Dbal\Event;

use Web\Framework\Dbal\Entity;
use Web\Framework\Event\Event;

class EntityPersist extends Event
{
    public function __construct(private Entity $entity)
    {
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

}