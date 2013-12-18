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
        $app = $this->app;

        ORM::configure($config->storage->mysql->connection);
        ORM::configure('username', $config->storage->mysql->user);

        $app->view(new \Slim\Views\Twig());
        $app->view->parserOptions = array(
            'charset' => 'utf-8',
            // 'cache' => __DIR__.'/../../../templates/cache',
            'auto_reload' => true,
            'strict_variables' => false,
            'autoescape' => true
        );
        $app->view->parserExtensions = array(new \Slim\Views\TwigExtension());
        $app->view->setData('request', $app->request);
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