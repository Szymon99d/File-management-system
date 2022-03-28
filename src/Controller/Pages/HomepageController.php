<?php

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomepageController extends AbstractController
{
    #[Route('/',name:'app_homepage')]
    public function homepage(): Response
    {
        return $this->render('pages/homepage.html.twig');
    }
}


?>