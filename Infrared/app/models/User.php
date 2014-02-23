<?php

class User extends \Phalcon\Mvc\Model
{
    const salt = 'r7(LX87g;kL4mufL8o';

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

    public function getDomain($id)
    {
        return Domain::query()
                ->where("user_id = :user_id:")
                ->andWhere("id = :id:")
                ->bind(array('user_id' => $this->id, 'id' => $id))
                ->execute()
                ->getFirst();
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

    public function getConfirmationCode()
    {
        return md5(self::salt.$this->id);
    }
}