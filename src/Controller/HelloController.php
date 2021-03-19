<?php

namespace App\Controller;

use App\Service\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hello")
 */

class HelloController extends AbstractController {

    /**
     * @Route("/{name}")
     */

    public function world(StringUtils $stringUtils, $name) {
        $name = $stringUtils->capitalize($name);
        return $this->render('hello.html.twig', ['name' => $name]);
    }
}

