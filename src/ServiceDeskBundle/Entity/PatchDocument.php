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
class PatchDocument
{
    /**
     * @Type("array")
     * @var array
     */
    private $attributes;

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
