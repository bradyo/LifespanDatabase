<?php

namespace Application\Model;

class DefaultUser extends User
{
    public function __construct() {
        parent::__construct();
        $this->role = self::ROLE_GUEST;
        $this->status = self::STATUS_ACTIVE;
        $this->timezone = 'America/Los_Angeles';
        $this->locale = 'en_US';
        $this->name = 'Anonymous';
    }
}
