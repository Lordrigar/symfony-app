<?php

namespace App\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\LogDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTimeImmutable;

/**
 * @Route("/mongo")
 */
class MongoController extends AbstractController
{
    /**
     * var DocumentManager
     */
    private $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @Route("/")
     */
    public function index(): JsonResponse
    {
        $now = (new DateTimeImmutable())->format('Y-m-d');
        $message = new LogDocument();
        $message->setMessage('This is a new mongo message')
            ->setTimestamp($now);

    
        $this->dm->persist($message);
        $this->dm->flush();

        return $this->json('Message saved');
    }

    /**
     * @Route("/read")
     */
    public function read(): JsonResponse
    {
        $logs = $this->dm->getRepository(LogDocument::class)->findAll();

        return $this->json($logs, 200, [], ['groups' => 'LogDocument']);
    }
}
