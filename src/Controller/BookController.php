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
use App\Form\BookType1Type;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

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
    public function fetch1(ManagerRegistry $mr,EntityManagerInterface $em , Request $request , BookRepository $repo1): Response
    {
        $repo=$mr->getRepository(Book::class);
        $result=$repo->findAll();
        $result1=$repo1->findAll();
        //$req=$em->createQuery("select s from App\Entity\Student s where s.name = :n ");
        if($request->isMethod('post'))
        {
           // $value=$request->get('test');
            $result1=$repo1->fetchBookByPublished1();
           // dd($result);      
        }
        return $this->render('book/test.html.twig', [
            'response' => $result,
            'book1'=>$result1
        ]);
    }
    #[Route('/update1/{id}', name: 'update1')]
    public function update1(BookRepository $repo,ManagerRegistry $mr,Request $req,$id): Response
    {
        $book=$repo->find($id);//Recupération
        $form=$this->createForm(BookType1Type::class,$book);//besh talkaha meebya l form
        $form->handleRequest($req);
            if ($form->isSubmitted()&& $form->isValid())
            {
                $em=$mr->getManager();//3-Persist+Flush
                $em->persist($book);
                $em->flush();
                return $this->redirectToRoute('fetch1');
            }
        
        return $this->render('book/update.html.twig',['f'=>$form->createView()]);
	//ken amalt creation mtaa formulaire ekher lel update
      // ou bien : return $this->renderForm('student/update.html.twig',['f'=>$form]);
    }
    #[Route('/remove/{id}', name: 'remove')]
    public function remove(ManagerRegistry $mr,BookRepository $repo,$id): Response
    {
       $entite=$repo->find($id);
        
        if(!$entite)
        {
            throw $this->createNotFoundException('Aucune entité trouvée avec ce nom.');
        }
        $em=$mr->getManager();
        $em->remove($entite);
        $em->flush();
        return $this->redirectToRoute('fetch1');    
    }
   
    #[Route('/details/{id}', name: 'book_details')]
    public function bookDetails(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }
}
