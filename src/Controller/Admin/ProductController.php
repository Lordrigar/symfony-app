<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     * 
     * @param ProductRepository $productRepository
     * @return new JsonResponse
     */
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'Product']);
    }

    /**
     * @Route("/product/create/{name}/{price}", name="product_create")
     * @param string $name
     * @param int $price
     * 
     * @return new JsonResponse
     */
    public function create(string $name, int $price): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }
}
