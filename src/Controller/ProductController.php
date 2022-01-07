<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Knp\Component\Pager\PaginatorInterface;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Product Controller.
 *
 * @Route("/products")
 *
 */
class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct( ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/", name="product_index")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->entityManager->createQueryBuilder();

        $query->select('p')
            ->from(Product::class, 'p');

        $products = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/new", name="create_product")
     */
    public function createProduct(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            $immutable = DateTimeImmutable::createFromMutable( new \DateTime('now') );
            $product->setCreatedAt($immutable);
            $product->setUpdatedAt($immutable);

            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Product has been created successfully');
            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="products_show")
     */
    public function showAction( Product $product): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($product);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);

    }

    /**
     * Displays a form to edit Product Entity.
     *
     * @Route("/edit/{id}", name="products_edit")
     * @Method({"GET", "POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Product            
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {        
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            $immutable = DateTimeImmutable::createFromMutable( new \DateTime('now') );
            $product->setUpdatedAt($immutable);

            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Product has been edited successfully');
            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/edit.html.twig', [
            'form' => $form,
        ]);
    } 
    
    /**
     * Deletes a Product entity.
     *
     * @Route("/delete/{id}", name="products_delete")
     * @Method("DELETE")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $product =  $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($product);        
        $this->entityManager->flush();

        $this->addFlash('success', 'Product has been deleted successfully');

        $res = [
            'type'=>'success',
            'message'=>'Se eliminó Correctamente'
        ];

        return new JsonResponse($res);
    }
    
}
