<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
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
 * Category Controller.
 *
 * @Route("/categories")
 *
 */
class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct( ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/", name="category_index")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->entityManager->createQueryBuilder();

        $query->select('c')
            ->from(Category::class, 'c');

        $categories = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="create_category")
     */
    public function createCategory(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            $immutable = DateTimeImmutable::createFromMutable( new \DateTime('now') );
            $category->setCreatedAt($immutable);
            $category->setUpdatedAt($immutable);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'Category has been created successfully');
            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Displays a form to edit Category Entity.
     *
     * @Route("/edit/{id}", name="categories_edit")
     * @Method({"GET", "POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Category            
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Category $category)
    {        
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            $immutable = DateTimeImmutable::createFromMutable( new \DateTime('now') );
            $category->setUpdatedAt($immutable);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'Category has been edited successfully');
            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/edit.html.twig', [
            'form' => $form,
        ]);
    } 

    /**
     * Deletes a Category entity.
     *
     * @Route("/delete/{id}", name="categories_delete")
     * @Method("DELETE")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $category =  $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($category);        
        $this->entityManager->flush();

        $this->addFlash('success', 'Category has been deleted successfully');

        $res = [
            'type'=>'success',
            'message'=>'Se eliminó Correctamente'
        ];

        return new JsonResponse($res);
    }
}
