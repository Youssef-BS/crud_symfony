<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StudentController extends AbstractController
{   
    private $firstName="";
    #[Route('/student/{username}', name: 'app_student')]
    public function index($username): Response
    {
        $var = 123 ; 
        return $this->render('student/index.html.twig', [
            'username' => $username,
            'v'=>$var,
            'FN'=>$this->firstName
        ]);
    }
}
