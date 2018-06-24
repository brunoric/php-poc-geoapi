<?php
/**
 * This file is part of the php-poc-geoapi console application.
 *
 * (c) Bruno Ricardo Siqueira <brunoric@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\RepositoryInterface;

/**
 * Class CustomerController
 * @package App\Controller
 */
class CustomerController
{
    protected $logger;
    protected $repository;

    /**
     * CustomerController constructor.
     *
     * @param LoggerInterface $logger
     * @param RepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, RepositoryInterface $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * Controller action to return a formatted JSON response with a list of customer details based on the criterias
     * passed as arguments.
     *
     * @Route("customer/list/{property}/{direction}")
     * @param string|null $property
     * @param string|null $direction
     * @return Response
     */
    public function list(?string $property = null, ?string $direction = null)
    {
        $customers = $this->repository->fetchAll($property, $direction);

        return new JsonResponse($customers);
    }
}