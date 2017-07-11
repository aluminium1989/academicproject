<?php
namespace UserBundle\Security;

use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const NUOVO = 'new';
    const DELETE = 'delete';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return self::ACCESS_GRANTED;
        }


        /** @var User $updatedUser */
        $updatedUser = $subject;
        switch ($attribute) {
            case self::VIEW:
                return self::ACCESS_GRANTED;
            case self::EDIT:
                return $this->canEdit($updatedUser, $token);
            case self::NUOVO:
                return self::ACCESS_DENIED;
            case self::DELETE:
                return self::ACCESS_DENIED;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(User $updatedUser, $token)
    {
        return $this->decisionManager->decide($token, array('ROLE_USER'))
            && ( $token->getUser()->getId() === $updatedUser->getId());
    }
}
