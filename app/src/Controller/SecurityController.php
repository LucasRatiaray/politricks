<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = null;
        $last_username = '';

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if (!$email || !$password || !$confirmPassword) {
                $error = 'Tous les champs sont obligatoires.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $error = 'Un compte existe déjà avec cet email.';
                } else {
                    $user = new User();
                    $user->setEmail($email);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('app_login');
                }
            }
            $last_username = $email;
        }

        return $this->render('security/register.html.twig', [
            'last_username' => $last_username,
            'error' => $error,
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = null;
        $success = null;
        $last_username = '';

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            if (!$email) {
                $error = 'Veuillez saisir votre adresse email.';
            } else {
                $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if (!$user) {
                    // Pour des raisons de sécurité, on affiche le même message même si l'email n'existe pas
                    $success = 'Si un compte existe avec cette adresse email, un lien de réinitialisation a été envoyé.';
                } else {
                    // Ici vous pourriez implémenter l'envoi d'email avec un token de réinitialisation
                    // Pour l'instant, on affiche juste un message de succès
                    $success = 'Si un compte existe avec cette adresse email, un lien de réinitialisation a été envoyé.';
                }
            }
            $last_username = $email;
        }

        return $this->render('security/forgot_password.html.twig', [
            'last_username' => $last_username,
            'error' => $error,
            'success' => $success,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
