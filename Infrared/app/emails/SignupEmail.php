<?php

class SignupEmail extends BaseEmail
{
    public $templateName = 'signup';

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