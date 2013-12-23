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
}