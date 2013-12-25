<?php

class LoginController extends \Phalcon\Mvc\Controller
{
    public function loginAction(){

        $email = $this->request->getPost("email");

        $user = User::query()->where("email = :email:")
                    ->bind(array("email" => $email))->execute()->getFirst();

        $sessionKey = bin2hex(openssl_random_pseudo_bytes(40));

        $scheme = $this->request->getScheme();
        $host = $this->request->getHttpHost();
        $path = $this->url->get(array('for' => 'validate_login', 'key' => $sessionKey));

        if(!$user) {
            // signup
            $user = new User;
            $user->email = $email;
            $user->api_key = bin2hex(openssl_random_pseudo_bytes(40));
            $this->session->set('email_sent', 'We\'ve emailed you a link to your new account!');
        } else {
            $this->session->set('email_sent', 'We\'ve emailed you a link to your account!');
        }
        $user->session_key = $sessionKey;
        $user->save();

        $mail = new LoginEmail($email, sprintf('%s://%s%s', $scheme, $host, $path));
        $this->mail_sender->send($mail);

        return $this->response->redirect(array('for' => 'front'));
    }

    public function validateAction()
    {
        $key = $this->dispatcher->getParam('key');

        $user = User::query()->where("session_key = :key:")
                    ->bind(array("key" => $key))->execute()->getFirst();

        if($user) {

            $this->session->set('auth', array('user_id' => $user->id)); // 1 year
            return $this->response->redirect(array('for' => 'domains'));
        }
        $this->flash->error('There seems to be something wrong with that link');
        return $this->response->redirect(array('for' => 'front'));
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect(array('for' => 'front'));
    }
}