<?php

namespace App\Security\Voter;

use App\Entity\Question;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuestionVoter extends Voter
{
    public const EDIT = 'QUESTION_EDIT';
    //public const VIEW = 'POST_VIEW';

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

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'QUESTION_EDIT':
                // on vérifie si on peut éditer

                
                break;
            /* case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break; */
        }

        return false;
    }
    private function canEdit(Question $question){

    }
}
