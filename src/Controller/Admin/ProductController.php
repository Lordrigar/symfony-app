<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
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

        return $this->json(
            $products,
            200, 
            [], 
            [
                'groups' => ['Product', 'Product.category', 'Category']
            ]
        );
    }

    /**
     * @Route("/product/create", name="product_create", methods={"POST"})
     * @param CategoryRepository $categoryRepository
     * @return new JsonResponse
     */
    public function create(
        Request $request,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        $fields = $request->getContent();
        $fields = json_decode($fields);
        $entityManager = $this->getDoctrine()->getEntityManager();

        $cat = $categoryRepository->findOneBy(['name' => $fields->category]);

        $product = new Product();
        $product->setName($fields->name)->setPrice($fields->price)->setCategory($cat);

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }
}
