<?php

class Domain extends \Phalcon\Mvc\Model
{
    public $id;

    public $user_id;

    public $domain_name;

    public $replacements;

    public function initialize()
    {
        $this->belongsTo("user_id", "User", "id");
    }

    public function getReplacements()
    {
        return $this->replacements
            ? json_decode($this->replacements, true)
            : array();
    }

    public function doReplacements($page)
    {
        $patterns = $this->getReplacements();
        return preg_replace(array_keys($patterns), array_values($patterns), $page);
    }

    public function getSource()
    {
        return "domains";
    }

    public function beforeCreate()
    {
        $exists = (bool) Domain::query()
            ->where("domain_name = :domain_name:")
            ->andWhere("user_id = :user_id:")
            ->bind(array("domain_name" => $this->domain_name, "user_id" => $this->user_id))
            ->execute()
            ->count();

        if($exists) {
            throw new DomainExistsException;
        }
    }

    public function beforeSave()
    {
        $valid = preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $this->domain_name) //valid chars check
                 && preg_match("/^.{1,253}$/", $this->domain_name) //overall length check
                 && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $this->domain_name); //length of each label

        if(!$valid) {
            throw new InvalidDomainNameException;
        }
    }

}