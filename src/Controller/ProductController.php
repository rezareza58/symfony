<?php
namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductController
{
    public function addProduct(
        Environment $twig, 
        FormFactoryInterface $factory, 
        Request $request,
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
        ){
        
        $product = new Product();
        $builder = $factory->createBuilder(FormType::class, $product);
        $builder->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('version', TextType::class)
            ->add('submit', SubmitType::class,
                [
                    'attr'=>[
                        'class'=>'btn-block btn-success'
                    ]
                ]
                );
        
         $form = $builder->getForm();
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid())
         {
             $manager->persist($product);
             $manager->flush();
             
             $session->getFlashBag()->add('info','Your product was created');
             
             return new RedirectResponse($urlGenerator->generate('homepage'));
         }
        
         return new Response(
            
            $twig->render(
                'Product/addProduct.html.twig',
                [
                    'formular' => $form->createView()
                ]
                )
            
            );
        
    }
}

