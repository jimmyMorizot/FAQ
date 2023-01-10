<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
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


        // on vérifie si l'utilisateur est moderator
        if ($this->security->isGranted('ROLE_MODERATOR')) return true; 


        // on vérifie si la reponse a un propriétaire
        if(null === $answer->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'ANSWER_VALIDATOR':
                // on vérifie si on peut valider
                return $this->canValidate($answer, $user);
                break;
            
        }

        return false;
    }

    // Je créer une méthode simple pour éviter de surcharger le switch

    private function canValidate(Answer $answer, User $user){
        // Le propriétaire de la reponse peut la modifier
        return $user === $answer->getQuestion()->getUser();
    }
}
