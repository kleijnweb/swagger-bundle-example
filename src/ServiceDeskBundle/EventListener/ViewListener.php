<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ViewListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        if ($result instanceof Resource) {
            $result = new Document($result);
        }
        if ($result instanceof Document) {
            $result->addLink('self', $event->getRequest()->getPathInfo());
            $event->setControllerResult($result->toArray());
        }
    }
}
