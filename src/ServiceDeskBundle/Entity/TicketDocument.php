<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity;

use JMS\Serializer\Annotation\Type;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class TicketDocument
{
    /**
     * @Type("KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\Ticket")
     * @var Ticket
     */
    private $attributes;

    /**
     * @return Ticket
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->getAttributes();
    }
}
