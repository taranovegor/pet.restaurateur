<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Factory\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ResponseFactory
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function create(mixed $data = null, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json');

        return new JsonResponse($json, $status, $headers, true);
    }
}
