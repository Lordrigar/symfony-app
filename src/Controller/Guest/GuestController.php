<?php

namespace App\Controller\Guest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GuestsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Guests;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\PageService;

class GuestController extends AbstractController
{
    /**
     * @Route("/guest", name="guest")
     * 
     * @param GuestsRepository $productRepository
     * @return JsonResponse
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
     * @return JsonResponse
     */
    public function create(string $name): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $guest = new Guests();
        $guest->setName($name)
        ->setSurname('Zed')
        ->setNickName('nickname');

        $entityManager->persist($guest);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/guest/create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postCreate(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $serializer = $this->container->get('serializer');
        $entity = $serializer->deserialize($request->getContent(), Guests::class, 'json');
        $errors = $validator->validate($entity);

        if ($errors->count() > 0){
            throw new BadRequestHttpException('Validation failed!');
        }

        $entityManager = $this->getDoctrine()->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();


        return $this->json(['Saved!']);
    }

    /**
     * @Route("/guest/search", methods={"POST"})
     */
    public function searchPaginate(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $pageService = new PageService($request, $em, Guests::class, 4);

        return $this->json(
            [
                'guests' => $pageService->getRecords(),
                'pager' => $pageService->getDisplayParameters(),
            ], 
            200, 
            [], 
            ['groups' => 'Guests']
        );
    }
}
