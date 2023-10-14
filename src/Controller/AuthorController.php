<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AuthorType;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorController extends AbstractController
{ 

    


    #[Route('/showAuthor/{name}', name: 'app_author')]
    public function index($name): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
            'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
            ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
            'taha.hussein@gmail.com', 'nb_books' => 300),
            );
        $size=sizeof($authors);
        return $this->render('author/index.html.twig', [
            'name' => $name,
            "authors" => $authors,
            "size" => $size,
        ]);
    }

    #[Route('/auhtorDetails/{id}', name: 'app_author2')]
    public function auhtorDetails($id) : Response {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
            'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
            ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
            'taha.hussein@gmail.com', 'nb_books' => 300),
            );

            
           
    return $this->render('author/showAuthors.html.twig', [
        "id"=>$id,
        "authors"=>$authors,
    ]);
    }

    #[Route('/getAll', name: 'app_listDB')]
    public function getAll(AuthorRepository $repo) : Response{

        $list = $repo->findAll();
        return $this->render('author/listDB.html.twig',[
          'authors'=>$list
        ]);
    }

    #[Route('/getOne/{id}', name: 'app_getOne')]
    public function getAuthor(AuthorRepository $repo, $id): Response
    {
        $author = $repo->find($id);
        
    
        if (!$author) {
            throw new NotFoundHttpException('author not found.');
        }

        $authorsWithBooks = $repo->findBooksByAuthorWithJoin($author);
        return $this->render('author/details.html.twig', [
            'author' => $author,
            'authorsWithBooks' => $authorsWithBooks,
        ]);

        
    }

    
    #[Route('/addAuther', name: 'author_add')]
    public function addAuther(Request $request, ManagerRegistry $manger): Response
    {
        $em = $manger->getManager();
        $username = $request->get('username');
        $email = $request->get('email');
        $author = new Author();
        $author->setUsername($username);
        $author->setEmail($email);
        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('app_listDB');
    }


    #[Route('/removeAuther/{id}', name: 'author_remove')]
    public function removeAuther(AuthorRepository $repo, EntityManagerInterface $entityManager  , $id) : Response {
        $author = $repo->find($id);
        if (!$author) {	    
            throw $this->createNotFoundException('Author not found');
        }
        $entityManager->remove($author);
        $entityManager->flush();
         return $this->redirectToRoute('app_listDB');
    }

    #[Route('/getOneToUpdate/{id}', name: 'app_getOneToUpdate')]
    public function updateAuther(AuthorRepository $repo , $id) : Response{
        $author = $repo->find($id);
        return $this->render('author/update.html.twig',[
          'author'=>$author,
        ]);
    }



    #[Route('/update/{id}', name: 'author_update')]
    public function updateAuthor(Request $req , ManagerRegistry $manager , $id , AuthorRepository $repo):Response 
{
    $em = $manager->getManager();
    $author = $repo->find($id);
    $form = $this->createForm(AuthorType::class , $author);
    $form->handleRequest($req);
   
    if($form->isSubmitted()){
    $em->persist($author);
    $em->flush();
    return $this-> redirectToRoute('app_listDB');
    }
    return $this->renderForm('author/update.html.twig',
    [
        'f' => $form
    ]
);
}

#[Route('/addBookToAuthor/{id}', name: 'addBookToAuthor')]
public function addBookToAuthorAction(Request $req, $id, ManagerRegistry $manager, AuthorRepository $repo): Response
{
    $em = $manager->getManager();
    $author = $repo->find($id);
    

    $ref = $req->get('ref');
    $title = $req->get('title');
    $date = $req->get('publication_date');
    $publicationDate = new \DateTime($date);
    $published = $req->get('published');
    $category = $req->get('category');

    $book = new Book();
    $book->setRef($ref); 
    $book->setTitle($title);
    $book->setPublicationDate($publicationDate);
    $book->setPublished($published === 'on');
    $book->setCategory($category);
    $book->setAuthor($author);
 
    if(!empty($ref)){
        $em->persist($book);
        $em->flush();
    }
  

        return $this-> redirectToRoute('app_listDB');
        
        
}



// public function showBooksByAuthorAction($id, AuthorRepository $authorRepository)
// {
//     $author = $authorRepository->find($id);

//     if (!$author) {
//         throw $this->createNotFoundException('Author not found');
//     }

//     $books = $authorRepository->findBooksByAuthor($author);
    
//     return $this->render('author/details.html.twig', [
//         'author' => $author,
//         'books' => $books, 
        
//     ]);
// }




}
