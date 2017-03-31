<?php declare(strict_types = 1);
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Controller;

use KleijnWeb\PhpApi\Descriptions\Description\Repository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class DescriptionRepositoryController
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
     * @param string $name
     * @param string $version
     * @return JsonResponse
     */
    public function get(string $name, $version = 'v1')
    {
        $description = $this->repository->get("$name/$version.yml");
        $definition = $description->getDocument()->getDefinition();
        return new JsonResponse($definition);
    }
}
