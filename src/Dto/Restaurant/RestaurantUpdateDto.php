<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Dto\Restaurant;

use App\Dto\Restaurant\Embedded\WorkingHoursDto;
use App\Dto\Restaurant\Fields\AddressFieldTrait;
use App\Dto\Restaurant\Fields\CoordinatesFieldTrait;
use App\Dto\Restaurant\Fields\DescriptionFieldTrait;
use App\Dto\Restaurant\Fields\PhoneFieldTrait;
use App\Dto\Restaurant\Fields\WorkingHoursFieldTrait;
use App\ValueObject\Coordinates;

readonly class RestaurantUpdateDto
{
    use DescriptionFieldTrait;
    use AddressFieldTrait;
    use CoordinatesFieldTrait;
    use PhoneFieldTrait;
    use WorkingHoursFieldTrait;

    /**
     * @param WorkingHoursDto[] $workingHours
     */
    public function __construct(
        string $description,
        string $address,
        Coordinates $coordinates,
        string $phone,
        array $workingHours
    ) {
        $this->description = $description;
        $this->address = $address;
        $this->coordinates = $coordinates;
        $this->phone = $phone;
        $this->workingHours = $workingHours;
    }
}
