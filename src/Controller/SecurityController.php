<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\RestPassType;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
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
            $user->setToken($tokenGenerator->generateToken());

            $manager->persist($user);
            $manager->flush();

            //création de l'email
            $email = (new TemplatedEmail())
                ->from('no-reply@snowtricks.com')
                ->to($user->getEmail())
                ->subject('Activation de votre compte')
                ->htmlTemplate('email/signup.html.twig')
                ->context([
                    'username' => $user->getUsername(),
                    'expiration_date' => new \DateTime('+2 days'),
                    'token' => $user->getToken(),
                ]);
            $mailer->send($email);

            $this->addFlash(
                'message',
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
        $user = $userRepo->findOneBy(['token' => $token]);
        // si aucun utilisateur existe avec token
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé !');
        }
        // suppression token
        $user->setToken(null);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash('message', 'Génial, votre compte est activé !');

        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/oubli-pass", name="security_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepo,
        EntityManagerInterface $manager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $form = $this->createForm(RestPassType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepo->findOneByEmail($data['email']);
            if (!$user) {
                $this->addFlash('danger', 'Cette adresse n\'existe pas !');
                return $this->redirectToRoute('home');
            }
            $token = $tokenGenerator->generateToken();
            try {
                $user->setToken($token);
                $manager->persist($user);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash(
                    'warning',
                    'Une erreur est survenue : ' . $e->getMessage()
                );
                return $this->redirectToRoute('security_login');
            }
            $url = $this->generateUrl('security_reset_password', [
                'token' => $token
            ], UrlGeneratorInterface::ABSOLUTE_URL
        );

            $email = (new TemplatedEmail())
                ->from('no-reply@snowtricks.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->htmlTemplate('email/reset_password.html.twig')
                ->context([
                    'url' => $url,
                    'username' => $user->getUsername(),
                    'expiration_date' => new \DateTime('+2 days'),
                ]);
            $mailer->send($email);
            $this->addFlash(
                'message',
                'Un email de réinitialisation de mot de passe vous a été envoyé'
            );
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/forgotten_password.html.twig', [
            'emailForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset_pass/{token}", name="security_reset_password")
     */
    public function resetPassword(
        $token,
        Request $request,
        UserRepository $userRepo,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager
    ) {
        $user = $userRepo->findOneBy(['token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('security_login');
        }
        if ($request->isMethod('POST')) {
            $user->setToken(null);

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                )
            );
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('message', 'Mot de passe modifié avec succès');
            return $this->redirectToRoute('security_login');
        } else {
            return $this->render('security/reset_password.html.twig', [
                'token' => $token,
            ]);
        }
    }
}
