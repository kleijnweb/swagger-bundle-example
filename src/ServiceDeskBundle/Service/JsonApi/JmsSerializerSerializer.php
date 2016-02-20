<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\JsonApi;

use JMS\Serializer\Serializer as JmsSerializer;
use Tobscure\JsonApi\AbstractSerializer;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class JmsSerializerSerializer extends AbstractSerializer
{
    /**
     * @var JmsSerializer
     */
    private $serializer;

    /**
     * @param JmsSerializer $serializer
     */
    public function __construct(JmsSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null)
    {
        return $this->serializer->toArray($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getType($model)
    {
        return strtolower(basename(str_replace('\\', '/', get_class($model))));
    }

    /**
     * {@inheritdoc}
     */
    public function getId($model)
    {
        return $model->getId();
    }
}