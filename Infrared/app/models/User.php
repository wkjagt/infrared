<?php

class User extends \Phalcon\Mvc\Model
{
    public $id;

    public $email;

    public $api_key;

    public $session_key;

    public function getSource()
    {
        return "users";
    }
}