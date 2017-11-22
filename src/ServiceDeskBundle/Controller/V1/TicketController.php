<?php declare(strict_types = 1);
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Controller\V1;

use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Security\TicketOwnerVoter;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\Entity\TicketService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class TicketController
{
    /**
     * @var TicketService
     */
    private $service;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TicketService                 $service
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(TicketService $service, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->service              = $service;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage         = $tokenStorage;
    }

    /**
     * @param string $status
     * @param string $title
     * @param string $description
     * @param string $type
     * @param string $priority
     *
     * @return array
     */
    public function search(string $status = null, string $title = null, string $description = null, string $type = null, string $priority = null)
    {
        $this->assertAllowed(TicketOwnerVoter::SEARCH);

        return $this->service->search($status, $title, $description, $type, $priority);
    }

    /**
     * @param Ticket $ticket
     *
     * @return Ticket
     */
    public function post(Ticket $ticket)
    {
        $this->assertAllowed(TicketOwnerVoter::CREATE);

        return $this->service->create($ticket, $this->getCurrentUserName());
    }

    /**
     * @param Ticket $ticket
     *
     * @return Ticket
     */
    public function put(Ticket $ticket)
    {
        $this->assertAllowed(TicketOwnerVoter::UPDATE, $ticket);

        return $this->service->update($ticket);
    }

    /**
     * @param integer $id
     *
     * @return null
     */
    public function delete(int $id)
    {
        $this->assertAllowed(TicketOwnerVoter::DELETE, $id);

        $this->service->deleteById($id);

        return null;
    }

    /**
     * @param string $ticketNumber
     *
     * @return Ticket
     */
    public function findByTicketNumber(string $ticketNumber)
    {
        $this->assertAllowed(TicketOwnerVoter::FETCH, $ticketNumber);

        /** @var Ticket */
        if (!$ticket = $this->service->findByTicketNumber($ticketNumber)) {
            throw new NotFoundHttpException;
        }

        return $ticket;
    }

    /**
     * Unsecured operation
     *
     * @param integer $id
     *
     * @return Ticket
     */
    public function get(int $id)
    {
        $this->assertAllowed(TicketOwnerVoter::FETCH, $id);

        return $this->service->find($id);
    }

    /**
     * @param string            $operation
     * @param Ticket|string|int $subject
     */
    private function assertAllowed(string $operation, $subject = null)
    {
        if (!$this->authorizationChecker->isGranted($operation, $subject)) {
            throw new AccessDeniedException();
        }
    }

    private function getCurrentUserName(): string
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        /* @var $user UserInterface */
        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return '';
        }
        return $user->getUsername();
    }

}
