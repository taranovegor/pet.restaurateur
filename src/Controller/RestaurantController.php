<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Controller;

use App\Constant\Regex;
use App\Dto\Restaurant\RestaurantCreateDto;
use App\Dto\Restaurant\RestaurantUpdateDto;
use App\Dto\Restaurant\RestaurantViewDto;
use App\Factory\Response\ResponseFactory;
use App\Mapper\RestaurantMapper;
use App\Service\RestaurantService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route('/restaurants')]
#[OA\Tag('Restaurant')]
final readonly class RestaurantController
{
    public function __construct(
        private RestaurantService $service,
        private RestaurantMapper $mapper,
        private ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(methods: [Request::METHOD_POST])]
    #[OA\Post(
        description: 'Create a new restaurant in the system',
        summary: 'Create restaurant',
    )]
    #[OA\RequestBody(
        description: 'Restaurant data to create',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: RestaurantCreateDto::class)),
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Restaurant created successfully',
        content: new OA\JsonContent(ref: new Model(type: RestaurantViewDto::class)),
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: 'Validation failed for the submitted data',
        content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
    )]
    public function create(#[MapRequestPayload] RestaurantCreateDto $createDto): JsonResponse
    {
        $restaurant = $this->service->create($createDto);
        $viewDto = $this->mapper->mapViewDto($restaurant);

        return $this->responseFactory->create($viewDto, Response::HTTP_CREATED);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', requirements: ['id' => Regex::UUID_V7], methods: [Request::METHOD_GET])]
    #[OA\Get(
        description: 'Get restaurant details by ID',
        summary: 'Get restaurant',
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Restaurant details retrieved successfully',
        content: new OA\JsonContent(ref: new Model(type: RestaurantViewDto::class)),
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Restaurant not found',
        content: new OA\JsonContent(ref: '#/components/schemas/Error'),
    )]
    public function read(Uuid $id): JsonResponse
    {
        $restaurant = $this->service->get($id);
        $viewDto = $this->mapper->mapViewDto($restaurant);

        return $this->responseFactory->create($viewDto);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', requirements: ['id' => Regex::UUID_V7], methods: [Request::METHOD_POST])]
    #[OA\Post(
        description: 'Update an existing restaurant',
        summary: 'Update restaurant',
    )]
    #[OA\RequestBody(
        description: 'Restaurant data to update',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: RestaurantUpdateDto::class)),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Restaurant updated successfully',
        content: new OA\JsonContent(ref: new Model(type: RestaurantViewDto::class)),
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Restaurant not found',
        content: new OA\JsonContent(ref: '#/components/schemas/Error'),
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: 'Validation failed for the submitted data',
        content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
    )]
    public function update(Uuid $id, #[MapRequestPayload] RestaurantUpdateDto $updateDto): JsonResponse
    {
        $restaurant = $this->service->update($id, $updateDto);
        $viewDto = $this->mapper->mapViewDto($restaurant);

        return $this->responseFactory->create($viewDto);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', requirements: ['id' => Regex::UUID_V7], methods: [Request::METHOD_DELETE])]
    #[OA\Delete(
        description: 'Delete a restaurant by ID',
        summary: 'Delete restaurant',
    )]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Restaurant deleted successfully',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Restaurant not found',
        content: new OA\JsonContent(ref: '#/components/schemas/Error'),
    )]
    public function delete(Uuid $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->responseFactory->create(null, Response::HTTP_NO_CONTENT);
    }
}
