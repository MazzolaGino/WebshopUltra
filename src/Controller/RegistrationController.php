<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Contrôleur simple pour la page d'inscription.
 * Le reste de la logique est dans le Live Component.
 */
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        // Si déjà connecté, on redirige
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig');
    }

    /**
     * C'est cette route qui manquait !
     * Elle correspond au nom "app_verify_email" utilisé dans le Live Component.
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request, 
        VerifyEmailHelperInterface $verifyEmailHelper, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // Valider la signature de l'email
        try {
            $verifyEmailHelper->validateEmailConfirmationFromRequest(
                $request,
                (string) $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        // Marquer l'utilisateur comme vérifié
        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre adresse email a été vérifiée avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/inscription/confirmation', name: 'app_registration_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('registration/confirmation.html.twig');
    }
}