<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\EventListener;

use KleijnWeb\SwaggerBundle\Document\DocumentRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ResponseListener
{
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @param DocumentRepository $documentRepository
     */
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $headers = $event->getResponse()->headers;

        /**
         * Temporary
         * @see https://github.com/kleijnweb/swagger-bundle/issues/59
         */
        $document = $this->documentRepository->get($request->attributes->get('_definition'));

        switch ($document->getDefinition()->info->version) {
            case '2.0.0':
                $headers->set('Content-Type', 'application/vnd.api+json');
                break;
            default:
                //noop
        }
    }
}