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
use App\Dto\Restaurant\Fields\NameFieldTrait;
use App\Dto\Restaurant\Fields\PhoneFieldTrait;
use App\Dto\Restaurant\Fields\WorkingHoursFieldTrait;
use App\ValueObject\Coordinates;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class RestaurantViewDto
{
    #[Assert\Uuid(versions: [Assert\Uuid::V7_MONOTONIC])]
    #[SerializedName('id')]
    public Uuid $id;

    use NameFieldTrait;
    use DescriptionFieldTrait;
    use AddressFieldTrait;
    use CoordinatesFieldTrait;
    use PhoneFieldTrait;
    use WorkingHoursFieldTrait;

    /**
     * @param WorkingHoursDto[] $workingHours
     */
    public function __construct(
        Uuid $id,
        string $name,
        string $description,
        string $address,
        Coordinates $coordinates,
        string $phone,
        array $workingHours,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->address = $address;
        $this->coordinates = $coordinates;
        $this->phone = $phone;
        $this->workingHours = $workingHours;
    }
}
