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
        $ttl = (string) (7 * 24 * 60 * 60);

        // pipe all commands
        $commands = array(
            array('HMSET', $id,
                  'x', (string) $data->click->x,
                  'y', (string) $data->click->y,
                  'elapsed' , (string) $data->elapsed,
                  'centered',  $data->centered ? '1' : '0'
            ),

            // keys expire "automatically" but we expire elements from sets
            // using sorted sets that are sorted by timestamp
            array('EXPIRE', $id, $ttl),

            // sort by time() so we can delete older items more easily
            array('ZADD', "clicks_by_page:$domain:".$data->page, time(), $id),
        );
        phpiredis_multi_command_bs($this->conn, $commands);

        // remove all members from the sorted set of clicks for this page
        // that are older that $ttl for which the hashes are also expiring
        $this->expire("clicks_by_page:$domain:".$data->page, $ttl);
    }

    protected function expire($key, $ttl)
    {
        // the timestamp in the past before which all members should be deleted
        $expire = (string) (time() - $ttl);

        phpiredis_command_bs($this->conn, array(
            'ZREMRANGEBYSCORE',
            $key,
            '-inf', // lowest score : as early is possible
            $expire // highest score
        ));
    }

    public function retrieve($page, $domain)
    {        
        $key = sprintf("clicks_by_page:%s:%s", urldecode($domain), $page);
        $ids = phpiredis_command_bs($this->conn, array('SORT', $key, 'BY', '*->elapsed'));

        // apparently this is fine http://blog.jmoz.co.uk/python-redis-py-pipeline
        $clicks = array();
        foreach($ids as $id) {
            // we need to check if the click still exists because it could
            // possibly already have expired
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
