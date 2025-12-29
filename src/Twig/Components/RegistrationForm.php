<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Entity\UserAddress;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsLiveComponent]
class RegistrationForm extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Format d\'email invalide.')]
    public string $email = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins 8 caractères.')]
    public string $password = '';

    #[LiveProp(writable: true)]
    public string $passwordConfirm = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    public string $firstName = '';

    #[LiveProp(writable: true)]
    public string $lastName = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
    public string $address = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    public string $city = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire.')]
    public string $zipCode = '';

    #[LiveProp(writable: true)]
    public bool $isAgreed = false;

    #[LiveAction]
    public function save(
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer,
        string $mailerFrom,
        LoggerInterface $logger,
        VerifyEmailHelperInterface $verifyEmailHelper // Service pour générer l'URL signée
    ) {
        $this->validate();

        if ($this->password !== $this->passwordConfirm) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return;
        }

        if (!$this->isAgreed) {
            $this->addFlash('error', 'Vous devez accepter les conditions d\'utilisation.');
            return;
        }

        try {
            $user = new User();
            $user->setEmail($this->email);
            $user->setFirstName($this->firstName);
            $user->setLastName($this->lastName);
            $user->setPassword($hasher->hashPassword($user, $this->password));
            $user->setRole('customer');
            $user->setIsVerified(false); // Par défaut non vérifié

            $userAddress = new UserAddress();
            $userAddress->setUser($user);
            $userAddress->setAddressLine1($this->address);
            $userAddress->setCity($this->city);
            $userAddress->setZipCode($this->zipCode);
            $userAddress->setIsDefault(true);
            $userAddress->setType('both');

            $em->persist($user);
            $em->persist($userAddress);
            $em->flush();

            // Génération de la signature pour l'email de vérification
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email', // Assurez-vous que cette route existe dans votre RegistrationController
                (string) $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $emailMessage = (new TemplatedEmail())
                ->from(new Address($mailerFrom, 'Votre Boutique'))
                ->to($user->getEmail())
                ->subject('Bienvenue ! Veuillez confirmer votre email')
                ->htmlTemplate('emails/registration_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'signedUrl' => $signatureComponents->getSignedUrl(), // On passe enfin la variable manquante
                    'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
                ]);

            $mailer->send($emailMessage);

            $this->addFlash('success', 'Compte créé ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_login');

        } catch (\Exception $e) {
            $logger->error('Erreur inscription : ' . $e->getMessage());
            $this->addFlash('error', 'Une erreur est survenue lors de la création du compte.');
        }
    }
}