<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Repository\BookRepository;
class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/addF1', name: 'addF1')]
    public function addF1(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Initialisation de l'attribut "published" à true
            $book->setPublished(true);

            // Récupération de l'auteur (vous devrez adapter cela en fonction de votre application)
            $author = $book->getAuthor();

            // Incrémentation de l'attribut "nb_books" de l'entité "Author"
            $author->setNbBooks($author->getNbBooks1() + 1);

            // Enregistrement du livre en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('fetch1');
        }

        return $this->render('book/add.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    #[Route('/fetch1', name: 'fetch1')]
    public function fetch1(ManagerRegistry $mr): Response
    {
        $repo=$mr->getRepository(Book::class);
        $result=$repo->findAll();

        return $this->render('book/test.html.twig', [
            'response' => $result,
        ]);
    }
}
