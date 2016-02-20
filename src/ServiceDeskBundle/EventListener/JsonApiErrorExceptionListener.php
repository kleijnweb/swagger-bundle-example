<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\EventListener;

use KleijnWeb\SwaggerBundle\Exception\InvalidParametersException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class JsonApiErrorExceptionListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $logRef = uniqid();

        $exception = $event->getException();
        $code = $exception->getCode();
        $headers = ['Content-Type' => 'application/vnd.api+json'];

        if ($exception instanceof InvalidParametersException) {
            $severity = LogLevel::NOTICE;
            $statusCode = Response::HTTP_BAD_REQUEST;
        } else {
            if ($exception instanceof NotFoundHttpException) {
                $statusCode = Response::HTTP_NOT_FOUND;
                $severity = LogLevel::INFO;
            } else {
                if ($exception instanceof AuthenticationException) {
                    $statusCode = Response::HTTP_UNAUTHORIZED;
                    $severity = LogLevel::WARNING;
                } else {
                    $is3Digits = strlen($code) === 3;
                    $class = (int)substr($code, 0, 1);
                    if (!$is3Digits) {
                        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                        $severity = LogLevel::CRITICAL;
                    } else {
                        switch ($class) {
                            case 4:
                                $severity = LogLevel::NOTICE;
                                $statusCode = Response::HTTP_BAD_REQUEST;
                                break;
                            case 5:
                                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                                $severity = LogLevel::ERROR;
                                break;
                            default:
                                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                                $severity = LogLevel::CRITICAL;
                        }
                    }
                }
            }
        }

        $jsonApiError = [
            'id'      => $logRef,
            'status'  => $statusCode,
            'title'   => Response::$statusTexts[$statusCode]
        ];

        $reference = $logRef ? " [logref $logRef]" : '';

        $event->setResponse(new JsonResponse(['errors' => $jsonApiError], $statusCode, $headers));

        $this->logger->log($severity, "{$jsonApiError['title']}{$reference}: $exception");
    }
}
