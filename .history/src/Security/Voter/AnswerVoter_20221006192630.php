<?php

namespace App\Security\Voter;

use App\Entity\Answer;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnswerVoter extends Voter
{
    public const VALIDATOR = 'ANSWER_VALIDATOR';
    
    // Je récupére les informations de sécurité en privé
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ["ANSWER_VALIDATOR"])
            && $subject instanceof Answer;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // pour forcer l'autocompletion sur un objet
        /**
         * @var Answer $answer
         */
        $answer = $subject;


        // on vérifie si l'utilisateur est admin, user ou moderator
        if ($this->security->isGranted('ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER')) return true; 

        // on vérifie si la question a un propriétaire
        if(null === $question->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
