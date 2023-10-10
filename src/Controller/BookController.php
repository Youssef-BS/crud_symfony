<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    #[Route('/getAllbook', name: 'app_listDBook')]
    public function getAll(BookRepository $repo) : Response{

        $list = $repo->findAll();
        return $this->render('book/index.html.twig',[
          'books'=>$list
        ]);
    }

    #[Route('/removeBook/{ref}', name: 'book_remove')]
    public function removeAuther(BookRepository $repo, EntityManagerInterface $entityManager  , $ref) : Response {
        $book = $repo->find($ref);
        if (!$book) {	    
            throw $this->createNotFoundException('Book not found');
        }
        $entityManager->remove($book);
        $entityManager->flush();
         return $this->redirectToRoute('app_listDBook');
    }

}
