<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//les includes eli tzedou 
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/fetch', name: 'fetch')]
    public function fetch(ManagerRegistry $mr): Response
    {
        $repo=$mr->getRepository(Author::class);
        $result=$repo->findAll();

        return $this->render('author/test.html.twig', [
            'response' => $result,
        ]);
    }
    #[Route('/addS', name: 'addS')]
    public function addS(ManagerRegistry $mr): Response
    {
        $a=new Author();
        $a->setUsername('nourreddine');
        $a->setEmail('nourreddine.chouk@gmail.com');
        $em=$mr->getManager();
        $em->persist($a);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }
    #[Route('/addF', name: 'addF')]
    public function addF(ManagerRegistry $mr,Request $req): Response
    {
       
        $a=new Author();//1-Instance
        $form=$this->createForm(AuthorType::class,$a);//creation mtaa formulaire
        $form->handleRequest($req);//taamel l'analyse mtaa la requete http ou besh trecuperi les infos necessaires
            if ($form->isSubmitted() && $form->isValid())//ken meebya ou tbaathet
            {
                $em=$mr->getManager();//3-Persist+Flush
                $em->persist($a);
                $em->flush();
                return $this->redirectToRoute('fetch');
            }
        
        return $this->render('author/add.html.twig',['f'=>$form->createView()]);
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(AuthorRepository $repo,ManagerRegistry $mr,Request $req,$id): Response
    {
        $a=$repo->find($id);//Recupération
        $form=$this->createForm(AuthorType::class,$a);//besh talkaha meebya l form
        $form->handleRequest($req);
            if ($form->isSubmitted()&& $form->isValid())
            {
                $em=$mr->getManager();//3-Persist+Flush
                $em->persist($a);
                $em->flush();
                return $this->redirectToRoute('fetch');
            }
        
        return $this->render('author/add.html.twig',['f'=>$form->createView()]);
	//ken amalt creation mtaa formulaire ekher lel update
      // ou bien : return $this->renderForm('student/update.html.twig',['f'=>$form]);
    }
    #[Route('/remove1/{id}', name: 'remove')]
    public function remove1(ManagerRegistry $mr,AuthorRepository $repo,$id): Response
    {
       $entite=$repo->find($id);
        
        if(!$entite)
        {
            throw $this->createNotFoundException('Aucune entité trouvée avec ce nom.');
        }
        $em=$mr->getManager();
        $em->remove($entite);
        $em->flush();
        return $this->redirectToRoute('fetch');    
    }
    #[Route('/delete', name: 'delete')]
    public function deleteAuthorsWithoutBooks(EntityManagerInterface $em)
{
    $authorRepository = $em->getRepository(Author::class);
    $authors = $authorRepository->findAll();

    foreach ($authors as $author) {
        $author->deleteIfNoBooks($em);
    }
    return $this->redirectToRoute('fetch');
}
}
