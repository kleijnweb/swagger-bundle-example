<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\EventListener;

use KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Service\JsonApi\JmsSerializerSerializer;
use KleijnWeb\SwaggerBundle\Document\DocumentRepository;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ViewListener
{
    /**
     * @var JmsSerializerSerializer
     */
    private $serializer;

    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * ViewListener constructor.
     *
     * @param DocumentRepository      $documentRepository
     * @param JmsSerializerSerializer $serializer
     */
    public function __construct(DocumentRepository $documentRepository, JmsSerializerSerializer $serializer)
    {
        $this->documentRepository = $documentRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $document = $this->documentRepository->get($request->attributes->get('_definition'));

        switch ($document->getDefinition()->info->version) {
            case '2.0.0':
                $result = $event->getControllerResult();
                if (!$result instanceof Resource && !$result instanceof Document) {
                    $result = new Resource($result, $this->serializer);
                }
                if ($result instanceof Resource) {
                    $result = new Document($result);
                }
                if ($result instanceof Document) {
                    $result->addLink('self', $event->getRequest()->getPathInfo());
                    $event->setControllerResult($result->toArray());
                }
                break;
            default:
                //noop
        }
    }
}
