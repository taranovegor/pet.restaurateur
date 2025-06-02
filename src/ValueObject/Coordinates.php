<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\ValueObject;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class Coordinates
{
    #[Assert\Type('float')]
    #[Assert\Range(min: -90, max: 90)]
    #[SerializedName('latitude')]
    public float $latitude;

    #[Assert\Type('float')]
    #[Assert\Range(min: -180, max: 180)]
    #[SerializedName('longitude')]
    public float $longitude;

    public function __construct(
        float $latitude,
        float $longitude,
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
