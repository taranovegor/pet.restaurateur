<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Event\Restaurant;

use App\Entity\Restaurant\Restaurant;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractRestaurantEvent extends Event
{
    public function __construct(
        private readonly Restaurant $restaurant,
    ) {
    }

    public function getRestaurant(): Restaurant
    {
        return $this->restaurant;
    }
}
