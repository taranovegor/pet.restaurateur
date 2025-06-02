<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Mapper;

use App\Dto\Restaurant\Embedded\WorkingHoursDto;
use App\Dto\Restaurant\RestaurantCreateDto;
use App\Dto\Restaurant\RestaurantUpdateDto;
use App\Dto\Restaurant\RestaurantViewDto;
use App\Entity\Restaurant\Restaurant;
use App\Entity\Restaurant\WorkingHours;

readonly class RestaurantMapper
{
    public function __construct(
        private WorkingHoursMapper $workingHoursMapper,
    ) {
    }

    public function mapViewDto(Restaurant $restaurant): RestaurantViewDto
    {
        return new RestaurantViewDto(
            $restaurant->getId(),
            $restaurant->getName(),
            $restaurant->getDescription(),
            $restaurant->getAddress(),
            $restaurant->getCoordinates(),
            $restaurant->getPhone(),
            $restaurant->getWorkingHours()->map(
                fn(WorkingHours $wh): WorkingHoursDto => $this->workingHoursMapper->mapDto($wh)
            )->getValues(),
        );
    }

    public function mapCreateDtoToEntity(RestaurantCreateDto $dto): Restaurant
    {
        $restaurant = new Restaurant(
            $dto->name,
            $dto->description,
            $dto->address,
            $dto->coordinates,
            $dto->phone,
        );

        foreach ($dto->workingHours as $workingHourDto) {
            $workingHours = $this->workingHoursMapper->mapDtoToEntityForRestaurant($restaurant, $workingHourDto);
            $restaurant->addWorkingHours($workingHours);
        }

        return $restaurant;
    }

    public function mapUpdateDtoToEntity(RestaurantUpdateDto $dto, Restaurant $restaurant): void
    {
        $restaurant->setDescription($dto->description);
        $restaurant->setAddress($dto->address);
        $restaurant->setCoordinates($dto->coordinates);
        $restaurant->setPhone($dto->phone);

        $daysToKeep = [];
        foreach ($dto->workingHours as $workingHoursDto) {
            $workingHours = $restaurant->findWorkingHours($workingHoursDto->dayOfWeek);
            if ($workingHours) {
                $this->workingHoursMapper->mapToEntity($workingHours, $workingHoursDto);
            } else {
                $workingHours = $this->workingHoursMapper->mapDtoToEntityForRestaurant($restaurant, $workingHoursDto);
            }
            $daysToKeep[$workingHours->getDayOfWeek()] = true;
            $restaurant->addWorkingHours($workingHours);
        }

        for ($dayOfWeek = 1; $dayOfWeek <= 7; ++$dayOfWeek) {
            if (isset($daysToKeep[$dayOfWeek])) {
                continue;
            }
            $restaurant->removeWorkingHours($dayOfWeek);
        }
    }
}
