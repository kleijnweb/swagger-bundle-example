<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Controller\V1;

use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\Entity\TicketService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @param TicketService $service
     */
    public function __construct(TicketService $service)
    {
        $this->service = $service;
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
    public function search($status = null, $title = null, $description = null, $type = null, $priority = null)
    {
        return $this->service->search($status, $title, $description, $type, $priority);
    }

    /**
     * @param integer $id
     *
     * @return Ticket
     */
    public function get($id)
    {
        return $this->service->find($id);
    }

    /**
     * @param Ticket $ticket
     *
     * @return Ticket
     */
    public function post(Ticket $ticket)
    {
        return $this->service->create($ticket);
    }

    /**
     * @param Ticket $ticket
     *
     * @return Ticket
     */
    public function put(Ticket $ticket)
    {
        return $this->service->update($ticket);
    }

    /**
     * @param integer $id
     *
     * @return null
     */
    public function delete($id)
    {
        $this->service->deleteById($id);

        return null;
    }

    /**
     * @param string $ticketNumber
     *
     * @return Ticket
     */
    public function findByTicketNumber($ticketNumber)
    {
        /** @var Ticket */
        if (!$ticket = $this->service->findByTicketNumber($ticketNumber)) {
            throw new NotFoundHttpException;
        }

        return $ticket;
    }
}
