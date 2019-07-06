<?php

namespace App\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GuestsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Guests;

class GuestController extends AbstractController
{
    /**
     * @Route("/guest", name="guest")
     * 
     * @param GuestsRepository $productRepository
     * @return new JsonResponse
     */
    public function index(GuestsRepository $guestsRepository): JsonResponse
    {
        $guests = $guestsRepository->findAll();

        return $this->json($guests, 200, [], ['groups' => 'Guests']);
    }

    /**
     * @Route("/guest/create/{name}", name="guest_create")
     * @param string $name
     * 
     * @return new JsonResponse
     */
    public function create(string $name): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $guest = new Guests();
        $guest->setName($name);

        $entityManager->persist($guest);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }
}
