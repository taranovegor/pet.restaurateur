<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Dto\Restaurant\Fields;

use App\Dto\Restaurant\Embedded\WorkingHoursDto;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

trait WorkingHoursFieldTrait
{
    /**
     * @var WorkingHoursDto[]
     */
    #[Assert\Valid]
    #[Assert\Count(min: 1, max: 7)]
    #[SerializedName('working_hours')]
    public readonly array $workingHours;
}
