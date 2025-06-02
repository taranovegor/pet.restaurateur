<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Entity\Restaurant;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('working_hours')]
class WorkingHours
{
    #[ORM\Id]
    #[ORM\Column('id', 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(Restaurant::class, inversedBy: 'workingHours')]
    #[ORM\JoinColumn('restaurant_id', nullable: false, onDelete: 'CASCADE')]
    private Restaurant $restaurant;

    #[ORM\Column('day_of_week', 'smallint', options: ['unsigned' => true])]
    private int $dayOfWeek;

    #[ORM\Column('open_time', 'time_immutable')]
    public DateTimeImmutable $openTime;

    #[ORM\Column('close_time', 'time_immutable')]
    public DateTimeImmutable $closeTime;

    public function __construct(
        Restaurant $restaurant,
        int $dayOfWeek,
        DateTimeImmutable $openTime,
        DateTimeImmutable $closeTime,
    ) {
        $this->restaurant = $restaurant;
        $this->dayOfWeek = $dayOfWeek;
        $this->openTime = $openTime;
        $this->closeTime = $closeTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function getOpenTime(): DateTimeImmutable
    {
        return $this->openTime;
    }

    public function setOpenTime(DateTimeImmutable $openTime): void
    {
        $this->openTime = $openTime;
    }

    public function getCloseTime(): DateTimeImmutable
    {
        return $this->closeTime;
    }

    public function setCloseTime(DateTimeImmutable $closeTime): void
    {
        $this->closeTime = $closeTime;
    }
}
