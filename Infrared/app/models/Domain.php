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

    public function confirm($code, $subdomain = '')
    {
        if($subdomain) {
            $subdomain = trim($subdomain, '. ') . '.';
        }
        $url = sprintf('http://%s%s', $subdomain, $this->domain_name);

        $client = new Guzzle\Http\Client($url);
        try {
            $html = $client->get('/')->send()->getBody()->__toString();
        } catch(\Exception $e) {
            throw new DomainConfirmationException;
        }

        $crawler = new Symfony\Component\DomCrawler\Crawler($html);
        $elem = $crawler->filter('#'.$code);

        if(count($elem)) {
            $this->confirmed = 1;
            $this->save();
        } else {
            throw new DomainConfirmationException;
        }
    }

    public function getReplacements()
    {
        return $this->replacements
            ? json_decode($this->replacements, true)
            : array();
    }

    public function doReplacements($page)
    {
        $patterns = $this->getReplacements() ?: array();
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
            ->andWhere("confirmed = 1")
            ->bind(array("domain_name" => $this->domain_name))
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