<?php
namespace Infrared\Service;

use Infrared\Data\BaseEmail;

class MailSender
{
    protected $mandrill;

    public function __construct($config)
    {
        $this->mandrill = new \Mandrill($config->mandrill->api_key);
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