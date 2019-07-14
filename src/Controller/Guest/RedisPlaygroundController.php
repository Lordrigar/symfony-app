<?php

namespace App\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Cache\RedisCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/redis")
 */
class RedisPlaygroundController extends AbstractController
{
    /** var RedisCache */
    public $redis;

    /**
     * @param RedisCache $redis
     */
    public function __construct(RedisCache $redis)
    {
        $this->redis = $redis->getRedis();
    }

    /**
     * @Route("/")
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        $this->redis->set('message', 'Hello world');
        $value = $this->redis->get('message');

        return $this->json([
            $value
        ]);
    }

    /**
     * @Route("/playground", methods={"POST"})
     * @param string $key
     * @param string $value
     * 
     * @return JsonResponse
     */
    public function playGround(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent());
        $this->redis->set($body->key, $body->value);

        return $this->json('Value set properly');
    }

    /**
     * @Route("/counter/{method}")
     * @param string $method
     * 
     * @return JsonResponse
     */
    public function redisCounter(string $method): JsonResponse
    {
        if ($method === 'incr') {
            $this->redis->incr('counter');
        }

        if ($method === 'decr') {
            $this->redis->decr('counter');
        }

        $counter = $this->redis->get('counter');

        return $this->json('Current value is ' . $counter);
    }

    /**
     * @Route("/hashes/{key}")
     * @param string $key
     * 
     * @return JsonResponse
     */
    public function redisHashes(string $key): JsonResponse
    {
        $this->redis->hset($key, 'age', 29);
        $this->redis->hset($key, 'occupation', 'Developer');
        $this->redis->hset($key, 'colour', 'Black');

        $this->redis->hmset($key.'1', [
            'age' => 30,
            'occupation' => 'Wannabe graphic',
            'colour' => 'Pink, duh',
        ]);

        $data = $this->redis->hgetall($key);
        $data1 = $this->redis->hgetall($key.'1');

        return $this->json([$data, $data1]);
    }

    /**
     * @Route("/readredis/{key}")
     */
    public function readRedis(string $key): JsonResponse
    {
        if ($this->redis->exists($key)) {
            return $this->json($this->redis->get($key));
        }

        return $this->json('Set the key first');
    }
}
