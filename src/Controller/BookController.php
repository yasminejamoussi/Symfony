<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(BookRepository $repo): Response
    {
        $books = $repo->findAll();
        if(!$books) {
            return new Response('No books found !');
        }
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/showBook', name: 'book_show')]
    public function show(BookRepository $rep): Response
    {
        $books = $rep->findAll();
        return $this->render('book/show.html.twig', ['books'=>$books]);
    }
   

    #[Route('/addBook', name: 'app_book_add')]
    public function addBook(Request $req,ManagerRegistry $manager) {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        //$book->setRef($form->getData()->getRef());
        if($form->isSubmitted()){
            $book->setPublished(true);
        $manager->getManager()->persist($book);
        $manager->getManager()->flush();
        return $this->redirectToRoute('book_show');
        }
        return $this->render('book/add.html.twig',[
            'f'=>$form->createView()
        ]);
    }
   
    #[Route('/updateBook/{id}', name: 'app_book_update')]
    public function updateBook($id,BookRepository $repo,Request $req,ManagerRegistry $manager){
        $book=$repo->find($id);
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $manager->getManager()->flush();
        return $this->redirectToRoute('book_show');
        }
        return $this->render('book/add.html.twig',[
            'f'=>$form->createView()
        ]);
    }

    #[Route('/removeBook/{id}', name: 'app_book_remove')]
    public function deleteBook($id,BookRepository $repo,ManagerRegistry $manager){
        $book = $repo->find($id);
        $manager->getManager()->remove($book);
        $manager->getManager()->flush();
                return $this->redirectToRoute('book_show');

    }
    #[Route('/book/{id}', name: 'book_details')]
public function book_details($id, BookRepository $repo): Response
{
    $book = $repo->find($id);

    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    return $this->render('book/bookdetails.html.twig', [
        'book' => $book,
    ]);
}

}