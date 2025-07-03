<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig
    ) {
    }

    public function sendEmailVerification(User $user): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = new \DateTime('+24 hours');

        $user->setEmailVerificationToken($token);
        $user->setEmailVerificationTokenExpiry($expiry);
        
        $this->entityManager->flush();

        $verificationUrl = $this->urlGenerator->generate(
            'app_verify_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('noreply@politricks.com')
            ->to($user->getEmail())
            ->subject('Vérifiez votre adresse email - Politricks')
            ->html($this->twig->render('emails/verify_email.html.twig', [
                'user' => $user,
                'verification_url' => $verificationUrl
            ]));

        $this->mailer->send($email);
    }

    public function sendPasswordReset(User $user): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = new \DateTime('+1 hour');

        $user->setResetPasswordToken($token);
        $user->setResetPasswordTokenExpiry($expiry);
        
        $this->entityManager->flush();

        $resetUrl = $this->urlGenerator->generate(
            'app_reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('noreply@politricks.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe - Politricks')
            ->html($this->twig->render('emails/reset_password.html.twig', [
                'user' => $user,
                'reset_url' => $resetUrl
            ]));

        $this->mailer->send($email);
    }

    public function verifyEmailToken(string $token): ?User
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['emailVerificationToken' => $token]);

        if (!$user || !$user->getEmailVerificationTokenExpiry()) {
            return null;
        }

        if ($user->getEmailVerificationTokenExpiry() < new \DateTime()) {
            return null;
        }

        $user->setEmailVerified(true);
        $user->setEmailVerificationToken(null);
        $user->setEmailVerificationTokenExpiry(null);
        
        $this->entityManager->flush();

        return $user;
    }

    public function verifyResetToken(string $token): ?User
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['resetPasswordToken' => $token]);

        if (!$user || !$user->getResetPasswordTokenExpiry()) {
            return null;
        }

        if ($user->getResetPasswordTokenExpiry() < new \DateTime()) {
            return null;
        }

        return $user;
    }

    public function clearResetToken(User $user): void
    {
        $user->setResetPasswordToken(null);
        $user->setResetPasswordTokenExpiry(null);
        $this->entityManager->flush();
    }
}