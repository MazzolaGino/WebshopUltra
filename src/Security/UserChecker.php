<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserChecker
 * Vérifie l'état du compte après l'authentification.
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Vérifications avant l'authentification (ex: compte supprimé)
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Vérification du Soft Delete
        if ($user->getDeletedAt() !== null) {
            throw new CustomUserMessageAuthenticationException('Ce compte a été désactivé ou supprimé.');
        }
    }

    /**
     * Vérifications après l'authentification (ex: e-mail non vérifié)
     * On ajoute l'argument TokenInterface pour respecter la signature de l'interface.
     */
    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Bloquer l'accès si l'e-mail n'est pas vérifié
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                "Votre adresse e-mail n'a pas encore été confirmée. Veuillez vérifier votre boîte de réception pour valider votre compte."
            );
        }
    }
}