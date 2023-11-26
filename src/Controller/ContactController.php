<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController

{

    public function __construct(private ContactRepository $productRepository, private RequestStack $requestStack, private EntityManagerInterface $entityManager)
	{
    }

    #[Route('/contact', name: 'contact.index')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'contact' => $this->productRepository->findAll(),
        ]);
    }

       
     #[Route("/contact", name :'contact.index')]
    public function form(int $id = null): Response
    {
       //création d'un formulaire
       $entity = $id ? $this->productRepository-> find($id) : new Contact();
       $type = ContactType::class;
       $form = $this->createForm($type, $entity);
       //recuperer la saisie précedente dans la requête http
       $form->handleRequest
       ($this->requestStack->getMainRequest());

               if($form->isSubmitted() && $form->isValid()){

                   $this->entityManager->persist($entity);
                   $this->entityManager->flush();

                   $message = $id ? 'Message' : 'It\'s okay !';

                   $this->addFlash('notice', $message);

                   return $this->redirectToRoute('contact.index');
               }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
