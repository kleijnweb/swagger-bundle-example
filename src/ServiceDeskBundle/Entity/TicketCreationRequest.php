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
class TicketCreationRequest
{
    /**
     * @var TicketDocument
     * @Type("KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\TicketDocument")
     */
    private $data;

    /**
     * @return TicketDocument
     */
    public function getData()
    {
        return $this->data;
    }
}
