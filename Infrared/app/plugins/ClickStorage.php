<?php

use Predis\Client as RedisClient;

class ClickStorage
{
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function storeClick($data, $domain)
    {   
        // #  create unique id for new click
        $id = sprintf('click:%d', phpiredis_command_bs($this->conn, array('INCR', 'click:id')));

        // pipe all commands
        $commands = array(
            array('HMSET', $id,
                  'x', (string) $data->click->x,
                  'y', (string) $data->click->y,
                  'elapsed' , (string) $data->elapsed,
                  'centered',  $data->centered ? '1' : '0'
            ),
            array('EXPIRE', $id, (string) (7 * 24 * 60 * 60)),
            array('SADD', "clicks_by_page:$domain:".$data->page, $id),
        );
        phpiredis_multi_command_bs($this->conn, $commands);
    }

    public function retrieve($page, $domain)
    {        
        $domain = urldecode($domain);
        $ids = phpiredis_command_bs($this->conn, array(
            'SORT',
            "clicks_by_page:$domain:$page",
            'BY', '*->elapsed'
        ));

        // apparently this is fine http://blog.jmoz.co.uk/python-redis-py-pipeline
        $clicks = array();
        foreach($ids as $id) {
            if($click = phpiredis_command_bs($this->conn, array('HGETALL', $id))) {

                $clicks[] = array(
                    array_shift($click) => array_shift($click),
                    array_shift($click) => array_shift($click),
                    array_shift($click) => array_shift($click),
                    array_shift($click) => array_shift($click),                    
                );
            }
        }
        return $clicks;
    }
}
