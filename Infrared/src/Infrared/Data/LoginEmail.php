<?php

namespace Infrared\Data;

class LoginEmail extends BaseEmail
{
    public $templateName = 'login';

    public $address;

    public $mergeVars = array();

    public function __construct($email, $link) {
        $this->address = $email;
        $this->mergeVars = array(
            array(
                'name' => 'LOGINLINK',
                'content' => $link
            )
        );
    }
}