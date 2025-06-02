<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\PrePersist]
    public function getCreatedAt(): DateTimeImmutable
    {
        if (!isset($this->createdAt)) {
            $this->initCreatedAt();
        }

        return $this->createdAt;
    }

    protected function initCreatedAt(DateTimeImmutable $createdAt = new DateTimeImmutable()): void
    {
        if (isset($this->createdAt)) {
            return;
        }

        $this->createdAt = $createdAt;
    }
}
