<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Mapper;

use App\Dto\Restaurant\Embedded\WorkingHoursDto;
use App\Entity\Restaurant\Restaurant;
use App\Entity\Restaurant\WorkingHours;

readonly class WorkingHoursMapper
{
    public function mapDto(WorkingHours $workingHours): WorkingHoursDto
    {
        return new WorkingHoursDto(
            $workingHours->getDayOfWeek(),
            $workingHours->getOpenTime(),
            $workingHours->getCloseTime(),
        );
    }

    public function mapToEntity(WorkingHours $workingHours, WorkingHoursDto $dto): void
    {
        $workingHours->setOpenTime($dto->openTime);
        $workingHours->setCloseTime($dto->closeTime);
    }

    public function mapDtoToEntityForRestaurant(Restaurant $restaurant, WorkingHoursDto $dto): WorkingHours
    {
        return new WorkingHours(
            $restaurant,
            $dto->dayOfWeek,
            $dto->openTime,
            $dto->closeTime,
        );
    }
}
