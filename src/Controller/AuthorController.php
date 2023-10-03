<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Entity\Author;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

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
    public function getAuthor(AuthorRepository $repo , $id) : Response{
        $author = $repo->find($id);
        return $this->render('author/details.html.twig',[
          'author'=>$author
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

        return new Response("Author added successfully");
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
          'author'=>$author
        ]);
    }


    #[Route('/updateOne/{id}', name: 'app_update')]
    public function update(Request $request, AuthorRepository $repo, $id, ManagerRegistry $manager): Response
    {
        $em = $manager->getManager();

        // Find the author by ID
        $author = $repo->find($id);

        // Check if the author exists
        if (!$author) {
            // Handle the case where the author is not found, e.g., redirect to a 404 page
            throw $this->createNotFoundException('Author not found');
        }

        // Handle the form submission
        if ($request->isMethod('POST')) {
            // Retrieve form data
            $username = $request->request->get('username');
            $email = $request->request->get('email');

            // Update author properties
            $author->setUsername($username);
            $author->setEmail($email);

            // Persist changes to the database
            $em->flush();

            // Redirect to a route after successful update
            return $this->redirectToRoute('app_listDB');
        }

        // Render the form template with the existing author's data
        return $this->render('author/update.html.twig', [
            'author' => $author,
        ]);
    }

}
