<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Controller\V2;

use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\TicketCreationRequest;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\TicketReplaceRequest;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\TicketUpdateRequest;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\JsonApi\JmsSerializerSerializer;
use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\Entity\TicketService;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Resource;

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
     * @var JmsSerializerSerializer
     */
    private $serializer;

    /**
     * @param TicketService           $service
     * @param JmsSerializerSerializer $serializer
     */
    public function __construct(TicketService $service, JmsSerializerSerializer $serializer)
    {
        $this->service = $service;
        $this->serializer = $serializer;
    }

    /**
     * @param string $status
     * @param string $title
     * @param string $description
     * @param string $type
     * @param string $priority
     *
     * @return Document
     */
    public function search($status = null, $title = null, $description = null, $type = null, $priority = null)
    {
        $results = $this->service->search($status, $title, $description, $type, $priority);

        $collection = (new Collection($results, $this->serializer))
            ->with(['author', 'comments']);

        $document = new Document($collection);
        $document->addMeta('total', count($results));

        return $document;
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
     * @param TicketCreationRequest $ticketRequest
     *
     * @return Ticket
     */
    public function post(TicketCreationRequest $ticketRequest)
    {
        return $this->service->create(
            $ticketRequest
                ->getData()
                ->getTicket()
        );
    }

    /**
     * @param TicketReplaceRequest $ticketRequest
     *
     * @return Ticket
     */
    public function put(TicketReplaceRequest $ticketRequest)
    {
        return $this->service->update($ticketRequest->getData()->getTicket());
    }

    /**
     * @param string              $id
     * @param TicketUpdateRequest $ticketRequest
     *
     * @return Ticket
     */
    public function patch($id, TicketUpdateRequest $ticketRequest)
    {
        $ticket = $this->service->find($id);
        foreach ($ticketRequest->getData()->getAttributes() as $key => $value) {
            $setterName = "set{$key}";
            $ticket->$setterName($value);
        }

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
     * @return Resource
     */
    public function findByTicketNumber($ticketNumber)
    {
        return new Resource($this->service->findByTicketNumber($ticketNumber), $this->serializer);
    }

}
