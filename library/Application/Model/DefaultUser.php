<?php

class Application_Model_DefaultUser extends Application_Model_User
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
