<?php

class MailSender
{
    protected $mandrill;

    public function __construct($apiKey)
    {
        $this->mandrill = new \Mandrill($apiKey);
    }

    public function send(BaseEmail $email)
    {
        $result = $this->mandrill->messages->sendTemplate(
            $email->templateName,
            array(),
            array(
                'to' => array(
                    array(
                        'email' => $email->address,
                    )
                ),
                'global_merge_vars' => $email->mergeVars
            ),
            true // async
        );

    }
}