<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer
    ) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashPassword = $encoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashPassword);
            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);
            // activation_token generation
            $user->setActivationToken(bin2hex(openssl_random_pseudo_bytes(16)));

            $manager->persist($user);
            $manager->flush();

            //création de l'email
            $email = (new Email())
                ->from('jamingph@gmail.com')
                ->to($user->getEmail())
                ->subject('Activation de votre compte')
                ->text(
                    $this->renderView('email/activation.html.twig', [
                        'token' => $user->getActivationToken(),
                    ])
                );
            $mailer->send($email);
               
        $this->addFlash(
            'info',
            'Félicitations, votre compte a bie nété créé ! Pour l\'activer, il ne vous reste plus qu\'à cliquer
             sur le lien présent dans l\'email qui vient de vous être envoyé '
        );
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/activation/{token}", name="security_activation")
     */
    public function activation(
        $token,
        UserRepository $userRepo,
        EntityManagerInterface $manager
    ) {
        // verification si un utilisateur a ce token
        $user = $userRepo->findOneBy(['activation_token' => $token]);
        // si aucun utilisateur existe avec token
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé !');
        }
        // suppression token
        $user->setActivationToken(null);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash('info', 'Génial, votre compte est activé !');

        return $this->redirectToRoute('home');
    }
}
