<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

#[Route('/admin')]
class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private RequestStack $requestStack, private EntityManagerInterface $entityManager)
	{
    }

        #[Route('/product', name: 'admin.product.index')]
        public function index(): Response
        {
            return $this->render('admin/product/index.html.twig', [
                'products' => $this->productRepository->findAll(),
            ]);
        }

        #[Route('/product/form', name: 'admin.product.form')]
        #[Route('/product/update/{id}', name: 'admin.product.update')]
        public function form(int $id = null):Response
        {

                //création d'un formulaire
                $entity = $id ? $this->productRepository-> find($id) : new Product();
                $type = ProductType::class;
                $form = $this->createForm($type, $entity);
                //recuperer la saisie précedente dans la requête http
                $form->handleRequest
                ($this->requestStack->getMainRequest());

                        if($form->isSubmitted() && $form->isValid()){

                            $filename= ByteString::fromRandom(32)->lower();
                            $file = $entity->getImage();

                            if ($file instanceof UploadedFile) {
                                $fileExtention = $file->guessClientExtension();
                                $file->move('img', "$filename.$fileExtention");    
                                
                                
                                $entity->setImage("$filename.$fileExtention");
                            }




                            $this->entityManager->persist($entity);
                            $this->entityManager->flush();

                            $message = $id ? 'Product updated' : 'Product created';

                            $this->addFlash('notice', $message);

                            return $this->redirectToRoute('admin.product.index');
                        }

                return $this->render('admin/product/form.html.twig',[
                    'form' => $form->createView(),
                ]);
        }





        #[Route('/product/delete/{id}', name: 'admin.product.delete')]
        public function delete (int $id):RedirectResponse
        {
            //selectionner l'entité supérieur 
            $entity = $this->productRepository->find($id);

            //Supprimer l'entité
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            // message de confirmation 
            $this->addFlash('notice', 'Product deleted');

            //redirection 
            return $this->redirectToRoute('admin.product.index');
        }
    }

