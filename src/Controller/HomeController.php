<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {

        return new Response(
            '<h1>Cервис отображения профилей Facebook по id</h1><h3>Для получения информации отправьте GET запрос по адресу /pages/id</h3>'
        );
    }


}
