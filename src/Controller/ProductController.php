<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/list", name="app_product_index", methods={"GET"})
     */
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, int $pageId = 1): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $Cat = $request->query->get('category');
        $word = $request->query->get('name');
        $orderby = $request->query->get('orderBy');
        $sortBy = $request->query->get('sortBy');

        
        if(!(is_null($Cat)||empty($Cat))){
            $selectedCat=$Cat;
        }
        else
        $selectedCat='';


        $tempQuery = $productRepository->findMore($minPrice, $maxPrice, $Cat,$word,$sortBy,$orderby);
        $pageSize = 6;

    // load doctrine Paginator
        $paginator = new Paginator($tempQuery);

    // you can get total items
        $totalItems = count($paginator);

    // get total pages
        $numOfPages = ceil($totalItems / $pageSize);

    // now get one page's items:
        $tempQuery = $paginator
        ->getQuery()
        ->setFirstResult($pageSize * ($pageId - 1)) // set the offset
        ->setMaxResults($pageSize); // set the limit

        return $this->render('product/index.html.twig', [
            'products' =>  $tempQuery->getResult(),
            'selectedCat' => $selectedCat,
            'numOfPages' => $numOfPages
        ]);
    }

    /**
     * @Route("/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
