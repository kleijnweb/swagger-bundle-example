<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Security;

use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class TicketOwnerVoter extends Voter
{
    const CREATE = 'ticket.create';
    const UPDATE = 'ticket.update';
    const DELETE = 'ticket.delete';
    const FETCH  = 'ticket.fetch';
    const SEARCH = 'ticket.search';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->hasRole($token, 'ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::FETCH:
            case self::SEARCH:
            case self::CREATE:
                return true;
            case self::UPDATE:
            case self::DELETE:
                return $this->isOwner($token, $subject);
            default:
                throw new \InvalidArgumentException("Unsupported operation '$attribute'");
        }
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return 0 === strpos($attribute, 'ticket.');
    }

    /**
     * @param TokenInterface $token
     * @param string         $role
     * @return bool
     */
    private function hasRole(TokenInterface $token, string $role): bool
    {
        return $this->decisionManager->decide($token, [$role]);
    }

    /**
     * @param TokenInterface $token
     * @param Ticket         $ticket
     * @return bool
     */
    private function isOwner(TokenInterface $token, Ticket $ticket): bool
    {
        $username = null;

        if (($user = $token->getUser()) && $user instanceof UserInterface) {
            $username = $user->getUsername();
        }

        return $ticket->getOwner() === $username;
    }
}
