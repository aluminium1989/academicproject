<?php
namespace ProjectBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class ProjectVoter
 * @package ProjectBundle\Security
 */
class ProjectVoter extends Voter
{
    const MANAGE = 'manage';
    const VIEW = 'view';

    private $decisionManager;

    /**
     * ProjectVoter constructor.
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::MANAGE))) {
            return false;
        }

        return true;
    }

    /**
     * Allow project management for admin and manager.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool|int
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::VIEW:
                return self::ACCESS_GRANTED;
                break;
            case self::MANAGE:
                if ($this->decisionManager->decide($token, array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
                    return self::ACCESS_GRANTED;
                }
                break;
        }

        return false;
    }
}
