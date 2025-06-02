<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Dto\Restaurant\Fields;

use App\ValueObject\Coordinates;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

trait CoordinatesFieldTrait
{
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[SerializedName('coordinates')]
    public readonly Coordinates $coordinates;
}
