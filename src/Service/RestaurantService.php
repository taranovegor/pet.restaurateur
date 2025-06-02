<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Service;

use App\Dto\Restaurant\RestaurantCreateDto;
use App\Dto\Restaurant\RestaurantUpdateDto;
use App\Entity\Restaurant\Restaurant;
use App\Event\Restaurant\AbstractRestaurantCreatedEvent;
use App\Event\Restaurant\AbstractRestaurantDeletedEvent;
use App\Event\Restaurant\AbstractRestaurantUpdatedEvent;
use App\Exception\EntityNotFoundException;
use App\Mapper\RestaurantMapper;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RestaurantService
{
    public function __construct(
        private RestaurantMapper $mapper,
        private RestaurantRepository $repository,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(RestaurantCreateDto $createDto): Restaurant
    {
        $restaurant = $this->mapper->mapCreateDtoToEntity($createDto);

        $this->entityManager->wrapInTransaction(function () use ($restaurant) {
            $this->repository->persist($restaurant);
        });

        $this->eventDispatcher->dispatch(new AbstractRestaurantCreatedEvent($restaurant));

        return $restaurant;
    }

    public function get(Uuid $id): Restaurant
    {
        $repository = $this->repository->findActive($id);
        if (!$repository) {
            throw new EntityNotFoundException(className: Restaurant::class, id: $id);
        }

        return $repository;
    }

    public function update(Uuid $id, RestaurantUpdateDto $updateDto): Restaurant
    {
        $restaurant = $this->get($id);
        $this->entityManager->wrapInTransaction(function () use ($updateDto, $restaurant) {
            $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);
        });

        $this->eventDispatcher->dispatch(new AbstractRestaurantUpdatedEvent($restaurant));

        return $restaurant;
    }

    public function delete(Uuid $id): void
    {
        $restaurant = $this->get($id);
        $this->entityManager->wrapInTransaction(function () use ($restaurant) {
            $restaurant->deactivate();
        });

        $this->eventDispatcher->dispatch(new AbstractRestaurantDeletedEvent($restaurant));
    }
}
