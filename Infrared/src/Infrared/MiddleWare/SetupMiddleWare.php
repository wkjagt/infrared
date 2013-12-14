<?php
namespace Infrared\MiddleWare;

use Slim\Middleware;
use Infrared\Storage\RedisStorage;
use Twig_Environment, Twig_Loader_Filesystem, Twig_Extension_Debug;
use Infrared\Service\MailSender;
use ORM;


class SetupMiddleWare extends Middleware
{
    public function call()
    {
        $config = $this->app->settings['config'];

        ORM::configure($config->storage->mysql->connection);
        ORM::configure('username', $config->storage->mysql->user);

        $this->app->container->singleton('twig', function() {
            $twig = new Twig_Environment(
                new Twig_Loader_Filesystem(__DIR__.'/../../../templates'),
                array('debug' => true)
            );
            $twig->addExtension(new Twig_Extension_Debug());
            return $twig;
        });

        $this->app->context = array();

        $this->app->container->singleton('storage', function() use ($config) {
            $redisClient = new \Predis\Client( (array) $config->storage->redis->parameters );
            return new RedisStorage($redisClient);            
        });

        $this->app->container->singleton('mailsender', function() use ($config){
            return new MailSender($config);
        });

        $this->next->call();
    }
}