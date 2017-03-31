<?php declare(strict_types = 1);
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Security;

use KleijnWeb\PhpApi\Descriptions\Description\Repository;
use KleijnWeb\SwaggerBundle\EventListener\Request\RequestMeta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;


/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Decides whether the rule(s) implemented by the strategy matches the supplied request.
     *
     * @param Request $request The request to check for a match
     *
     * @return bool true if the request matches, false otherwise
     */
    public function matches(Request $request)
    {
        if(!$request->attributes->has(RequestMeta::ATTRIBUTE_URI)){
            return false;
        }
        $description = $this->repository->get($request->attributes->get(RequestMeta::ATTRIBUTE_URI));

        // Hack, see https://github.com/kleijnweb/php-api-descriptions/issues/8
        $definition = $description->getDocument()->getDefinition();
        $operationDefinition = $definition
            ->paths
            ->{$request->attributes->get(RequestMeta::ATTRIBUTE_PATH)}
            ->{strtolower($request->getMethod())};

        return isset($operationDefinition->security);
    }
}
