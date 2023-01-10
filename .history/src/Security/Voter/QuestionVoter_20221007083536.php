<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Question;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuestionVoter extends Voter
{
    public const QUESTION_EDIT = 'QUESTION_EDIT';
   
    // Je récupére les informations de sécurité en privé
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Cette méthode décide si ce Voter doit s'exécuter  (on appelera ensuite voteOnAttribute())
     */
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['QUESTION_EDIT'])
            && $subject instanceof \App\Entity\Question;
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
         * @var Question $question
         */
        $question = $subject;


        // on vérifie si l'utilisateur est admin, user ou moderator
        if ($this->security->isGranted('ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER')) return true; 

        // on vérifie si la question a un propriétaire
        if(null === $question->getUser()) return false;


        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'QUESTION_EDIT':
                // on vérifie si on peut éditer
                return $this->canEdit($question, $user);
                
                break;
            
        }

        return false;
    }

    // Je créer une méthode simple pour éviter de surcharger le switch

    private function canEdit(Question $question, User $user){
        // Le propriétaire de la question peut la modifier
        return $user === $question->getUser();
    }

}
