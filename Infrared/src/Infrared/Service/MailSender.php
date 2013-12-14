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
        // echo '<pre>file: '.__FILE__."\nline: ".__LINE__."\n".print_r($email->mergeVars, true).'</pre>';die;

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
            )
        );

    }
}