<?php
/**
 * This file is part of the php-poc-geoapi console application.
 *
 * (c) Bruno Ricardo Siqueira <brunoric@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

interface RepositoryInterface
{
    public function fetchAll(?string $property = null, ?string $direction = null);

    public function fetch(int $id);
}