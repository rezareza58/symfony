<?php
namespace App\Controller;


use Symfony\Component\Form\FormFactory;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Username;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UserController
{
    public function addUser(
        Environment $twig,
        FormFactoryInterface $factory,
        Request $request,
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        \Swift_Mailer $mailer
        ){
        
            $user = new Username();
            $builder = $factory->createBuilder(FormType::class, $user);
            $builder->add('username', TextType::class)
                ->add('firstname', TextType::class)
                ->add('lastname', TextType::class)
                ->add('email', EmailType::class)
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ))
                ->add('submit', SubmitType::class,
                    [
                        'attr'=>[
                            'class'=>'btn-block btn-success'
                        ]
                    ]
                    );
            
                
                $form = $builder->getform();
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid())
                {
                    $manager->persist($user);
                    $manager->flush();
                    
                    $message = new \Swift_Message();
                    $message->setFrom('wf3pm@localhost.com')
                    ->setTo($user->getEmail())
                    ->setSubject('Validate your accont')
                    ->setBody(
                        $twig->render(
                            'mail\account_creation.html.twig',
                            ['user' => $user]
                            )
                        );
                    $mailer->send($message);
                    $session->getFlashBag()->add('info', 'Your registration was successful' );
                    return new RedirectResponse($urlGenerator->generate('homepage'));
                }
                return new Response(
                    $twig->render(
                        'Register/register.html.twig',
                        [
                            'formular' => $form->createView()
                        ]
                        )
                    );
    }
    public function activateUser(
        $token, 
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
        )
    {
        $repository = $manager->getRepository(Username::class);
        $user = $repository->findOneByEmailToken($token);
        
        if (!$user)
        {
            throw new NotFoundHttpException('user not found');
        }
        $user->setActive(true);
        $user->setEmailToken(null);
        $manager->flush();
        
        $session->getFlashBag()->add('info', 'Your account is active' );
        return new RedirectResponse($urlGenerator->generate('homepage'));
    }
}