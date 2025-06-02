<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Dto\Restaurant\Embedded;

use DateTimeImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

readonly class WorkingHoursDto
{
    #[OA\Property(description: 'Monday = 1, Sunday = 7')]
    #[Assert\Range(min: 1, max: 7)]
    #[SerializedName('day_of_week')]
    public int $dayOfWeek;

    #[OA\Property(example: '09:00')]
    #[Assert\LessThan(propertyPath: 'closeTime')]
    #[Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i'])]
    #[SerializedName('open_time')]
    public DateTimeImmutable $openTime;

    #[OA\Property(example: '18:00')]
    #[Assert\GreaterThan(propertyPath: 'openTime')]
    #[Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i'])]
    #[SerializedName('close_time')]
    public DateTimeImmutable $closeTime;

    public function __construct(
        int $dayOfWeek,
        DateTimeImmutable $openTime,
        DateTimeImmutable $closeTime,
    ) {
        $this->dayOfWeek = $dayOfWeek;
        $this->openTime = $openTime;
        $this->closeTime = $closeTime;
    }
}
