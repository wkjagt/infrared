<?php

class User extends \Phalcon\Mvc\Model
{
    public $id;

    public $email;

    public $api_key;

    public $session_key;

    public function getDomains($parameters=null)
    {
        return $this->getRelated('Domain', $parameters);
    }

    public function initialize()
    {
        $this->hasMany("id", "Domain", "user_id");
    }

    public function getSource()
    {
        return "users";
    }

    public function beforeValidationOnCreate()
    {
        $this->created = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
    }

    public function beforeValidationOnUpdate()
    {
        $this->modified = date('Y-m-d H:i:s');
    }
}