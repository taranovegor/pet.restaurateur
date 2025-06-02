<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Dto\Restaurant\Fields;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

trait NameFieldTrait
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[SerializedName('name')]
    public readonly string $name;
}
