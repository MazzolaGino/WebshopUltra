<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Repository\UserRepository;
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

    // --- Propriétés Utilisateur ---
    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    public string $firstName = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    public string $lastName = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Format d\'email invalide.')]
    public string $email = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire.')]
    public string $phoneNumber = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins 8 caractères.')]
    public string $password = '';

    #[LiveProp(writable: true)]
    public string $passwordConfirm = '';

    // --- Propriétés Adresse ---
    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
    public string $addressLine1 = '';

    #[LiveProp(writable: true)]
    public string $addressLine2 = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    public string $city = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire.')]
    public string $zipCode = '';

    #[LiveProp(writable: true)]
    #[Assert\NotBlank(message: 'Le pays est obligatoire.')]
    public string $country = 'France';

    #[LiveProp(writable: true)]
    #[Assert\IsTrue(message: 'Vous devez accepter les conditions.')]
    public bool $isAgreed = false;

    #[LiveAction]
    public function save(
        EntityManagerInterface $em, 
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer,
        string $mailerFrom,
        LoggerInterface $logger,
        VerifyEmailHelperInterface $verifyEmailHelper
    ) {
        // 1. Validation automatique
        $this->validate();

        // 2. Validation manuelle
        if ($userRepository->findOneBy(['email' => $this->email])) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return;
        }

        if ($this->password !== $this->passwordConfirm) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return;
        }

        try {
            // 3. Création de l'utilisateur
            $user = new User();
            $user->setEmail($this->email);
            $user->setFirstName($this->firstName);
            $user->setLastName($this->lastName);
            $user->setPhone($this->phoneNumber);
            $user->setPassword($hasher->hashPassword($user, $this->password));
            $user->setRole('customer');
            $user->setIsVerified(false);

            // 4. Création de l'adresse
            $address = new UserAddress();
            $address->setUser($user);
            $address->setAddressLine1($this->addressLine1);
            $address->setAddressLine2($this->addressLine2);
            $address->setCity($this->city);
            $address->setZipCode($this->zipCode);
            $address->setCountry($this->country);
            $address->setIsDefault(true);
            $address->setType('both');

            $em->persist($user);
            $em->persist($address);
            $em->flush();

            // 5. Email de vérification
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                (string) $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $emailMessage = (new TemplatedEmail())
                ->from(new Address($mailerFrom, 'Votre Boutique'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre compte')
                ->htmlTemplate('emails/registration_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
                ]);

            $mailer->send($emailMessage);

            // 6. Redirection vers l'étape "Vérifiez vos emails"
            return $this->redirectToRoute('app_registration_check_email');

        } catch (\Exception $e) {
            $logger->error('Erreur inscription : ' . $e->getMessage());
            $this->addFlash('error', 'Une erreur technique est survenue.');
        }
    }
}