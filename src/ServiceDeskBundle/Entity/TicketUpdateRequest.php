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
class TicketUpdateRequest extends TicketReplaceRequest
{
    /**
     * @var int
     * @Type("integer")
     */
    private $id;

    /**
     * @var PatchDocument
     * @Type("KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Entity\PatchDocument")
     */
    private $data;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PatchDocument
     */
    public function getData()
    {
        return $this->data;
    }
}
