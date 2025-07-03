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
use App\Service\EmailService;

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

        // pour les messages d'erreur utilisateur
        $errorMessage = null;
        if ($error) {
            $errorMessage = 'Email ou mot de passe incorrect.';
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, EmailService $emailService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = null;
        $formData = [];

        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $telephone = $request->request->get('telephone');
            $dateNaissance = $request->request->get('dateNaissance');
            $nationalite = $request->request->get('nationalite');
            $profession = $request->request->get('profession');

            // Stockage des données pour réaffichage en cas d'erreur
            $formData = [
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'telephone' => $telephone,
                'dateNaissance' => $dateNaissance,
                'nationalite' => $nationalite,
                'profession' => $profession,
            ];

            // Validation des champs obligatoires
            if (!$email || !$password || !$confirmPassword || !$firstName || !$lastName) {
                $error = 'Tous les champs obligatoires doivent être remplis.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Veuillez saisir une adresse email valide.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                // Vérification si l'email existe déjà
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $error = 'Un compte existe déjà avec cet email.';
                } else {
                    // Création du nouvel utilisateur
                    $user = new User();
                    $user->setEmail($email);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    
                    // Champs optionnels
                    if ($telephone) {
                        $user->setTelephone($telephone);
                    }
                    if ($dateNaissance) {
                        $user->setDateNaissance(new \DateTime($dateNaissance));
                    }
                    if ($nationalite) {
                        $user->setNationalite($nationalite);
                    }
                    if ($profession) {
                        $user->setProfession($profession);
                    }

                    $em->persist($user);
                    $em->flush();

                    // Envoi de l'email de vérification
                    try {
                        $emailService->sendEmailVerification($user);
                        $this->addFlash('success', 'Votre compte a été créé avec succès ! Un email de vérification a été envoyé à votre adresse.');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', 'Votre compte a été créé mais l\'email de vérification n\'a pas pu être envoyé. Vous pouvez vous connecter normalement.');
                    }
                    
                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/register.html.twig', [
            'form_data' => $formData,
            'error' => $error,
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $em, EmailService $emailService): Response
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
                if ($user) {
                    try {
                        $emailService->sendPasswordReset($user);
                    } catch (\Exception $e) {
                        // En cas d'erreur, on ne révèle pas l'information pour des raisons de sécurité
                    }
                }
                // Pour des raisons de sécurité, on affiche toujours le même message
                $success = 'Si un compte existe avec cette adresse email, un lien de réinitialisation a été envoyé.';
            }
            $last_username = $email;
        }

        return $this->render('security/forgot_password.html.twig', [
            'last_username' => $last_username,
            'error' => $error,
            'success' => $success,
        ]);
    }

    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, EmailService $emailService): Response
    {
        $user = $emailService->verifyEmailToken($token);
        
        if (!$user) {
            $this->addFlash('error', 'Le lien de vérification est invalide ou a expiré.');
            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'Votre email a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request, EmailService $emailService, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $emailService->verifyResetToken($token);
        
        if (!$user) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if (!$password || !$confirmPassword) {
                $error = 'Veuillez remplir tous les champs.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $emailService->clearResetToken($user);
                
                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
