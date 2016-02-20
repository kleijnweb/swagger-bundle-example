<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\EventListener;

use KleijnWeb\SwaggerBundle\Document\DocumentRepository;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Zend\Http\Response;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class RequestListener
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
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();

        if ($request->getContent()) {
            $document = $this->documentRepository->get($request->attributes->get('_definition'));

            switch ($document->getDefinition()->info->version) {
                case '2.0.0':
                    if ($request->getContentType() !== 'application/vnd.api+json') {
                        $event->setResponse(new Response('', Response::HTTP_NOT_ACCEPTABLE));
                    }
                    break;
                default:
                    //noop
            }
        }
    }
}