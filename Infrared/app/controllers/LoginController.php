<?php

class LoginController extends \Phalcon\Mvc\Controller
{
    const maxAttempts = 2;

    /**
     * User entered an email address to login or signup. Check if the user exists:
     *    - existing user: set new login code and send email
     *    - new user: create user, and continue the same as existing user
     */
    public function loginAction()
    {
        $cache = $this->cache;
        $ip = $this->request->getClientAddress(true);
        $loginTrackString = sprintf('loginsfromip:%s', $ip);
        $email = $this->request->getPost("email");

        $attempts = phpiredis_command_bs($cache, array('GET', $loginTrackString));

        if($attempts >= self::maxAttempts) {
            return $this->response->redirect(array('for' => 'front'));
        }

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
            $mail = new SignupEmail($email, sprintf('%s://%s%s', $scheme, $host, $path));
        } else {
            // login
            $this->session->set('email_sent', 'We\'ve emailed you a link to your account!');
            $mail = new LoginEmail($email, sprintf('%s://%s%s', $scheme, $host, $path));
        }
        $user->session_key = $sessionKey;
        $user->save();

        // increment attemps for this ip
        phpiredis_multi_command_bs($cache, array( array('INCR', $loginTrackString),
                                                  array('EXPIRE', $loginTrackString, 300) ));

        $this->mail_sender->send($mail);

        return $this->response->redirect(array('for' => 'front'));
    }

    /**
     * Validate the login code that was sent by email. If the code exists, create
     * a session for this user and redirect to domains page
     */
    public function validateAction()
    {
        $key = $this->dispatcher->getParam('key');

        $user = User::query()->where("session_key = :key:")
                    ->bind(array("key" => $key))->execute()->getFirst();

        if($user) {
            $this->session->set('auth', array('user_id' => $user->id)); // 1 year
            $user->session_key = '';
            $user->save();
            return $this->response->redirect(array('for' => 'domains'));
        }
        $this->flash->error('There seems to be something wrong with that link');
        return $this->response->redirect(array('for' => 'front'));
    }

    /**
     * Logout by destroying the session and then redirect to front page
     */
    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect(array('for' => 'front'));
    }
}