<?php

namespace App\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Cache\RedisCache;

class RedisPlaygroundController extends AbstractController
{
    /** var RedisCache */
    public $redis;

    /**
     * @param RedisCache $redis
     */
    public function __construct(RedisCache $redis)
    {
        $this->redis = $redis;
    }
    /**
     * @Route("/redis/", name="redis_playground")
     */
    public function index()
    {
        $redis = $this->redis->getRedis();

        $redis->set('message', 'Hello world');
        $value = $redis->get('message');

        return $this->json([
            $value
        ]);
    }
}
