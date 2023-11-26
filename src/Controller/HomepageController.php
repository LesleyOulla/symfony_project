<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{

    public function __construct(private RequestStack $requestStack)
    {

    }


    #[Route('/', name: 'homepage.index')]
    public function index(): Response
    {

        /*
        Débogage : 
        dump : afficher la donnée dans la page
        dd (dump and die) : afficher la donnée puis stopper le script
        */
    //     $post = $this->requestStack->getMainRequest()->request->get('key');

    //     dd($post);

    // return new Response ('{ "key" : "value" }',
    //     Response :: HTTP_CREATED, [
    //         'Content-Type' => 'application/json'
    //     ]);

    return $this->render('homepage/index.html.twig',[
    'my_array' => ['value0', 'vvalue1', 'value2'],
    'assoc_array' =>[
   'key0' => 'value0',
   'key1' => 'value1',
   'key2' => 'value2',
    ],
     'now' => new \DateTime(),



    ]);
    }



    #[Route('/hello/{name}', name : 'homepage.hello')]
    public function hello(string $name): Response
    {
        return $this->render('homepage/hello.html.twig', [
            'name' => $name
        ]);

        }
}