<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Entity\Restaurant;

use App\Entity\ActiveTrait;
use App\Entity\CreatedAtTrait;
use App\Entity\UpdatedAtTrait;
use App\Repository\RestaurantRepository;
use App\ValueObject\Coordinates;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(RestaurantRepository::class)]
#[ORM\Table('restaurants')]
#[ORM\HasLifecycleCallbacks]
class Restaurant
{
    use ActiveTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\Column('id', 'uuid')]
    private Uuid $id;

    #[ORM\Column('name', 'string', 32)]
    private string $name;

    #[ORM\Column('description', 'string', 255)]
    private string $description;

    #[ORM\Column('address', 'string', 64)]
    private string $address;

    #[ORM\Column('coordinates', 'coordinates')]
    private Coordinates $coordinates;

    #[ORM\Column('phone', 'string', 16)]
    private string $phone;

    /**
     * @var Collection<int, WorkingHours>
     */
    #[ORM\OneToMany(WorkingHours::class, 'restaurant', ['persist', 'remove'], orphanRemoval: true, indexBy: 'dayOfWeek')]
    private Collection $workingHours;

    public function __construct(
        string $name,
        string $description,
        string $address,
        Coordinates $coordinates,
        string $phone,
    ) {
        $this->id = Uuid::v7();
        $this->name = $name;
        $this->description = $description;
        $this->address = $address;
        $this->coordinates = $coordinates;
        $this->phone = $phone;
        $this->workingHours = new ArrayCollection([]);
        $this->setUpdatedAtAsNow();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function setCoordinates(Coordinates $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return Collection<WorkingHours>
     */
    public function getWorkingHours(): Collection
    {
        return $this->workingHours;
    }

    public function findWorkingHours(int $dayOfWeek): ?WorkingHours
    {
        return $this->workingHours->get($dayOfWeek);
    }

    public function addWorkingHours(WorkingHours $workingHours): void
    {
        $dayOfWeek = $workingHours->getDayOfWeek();
        if ($this->workingHours->containsKey($dayOfWeek)) {
            $this->removeWorkingHours($dayOfWeek);
        }

        $this->workingHours->set($dayOfWeek, $workingHours);
    }

    public function removeWorkingHours(int $dayOfWeek): void
    {
        $this->workingHours->remove($dayOfWeek);
    }
}
