<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Repository;

use App\Entity\Restaurant\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[] findAll()
 * @method Restaurant[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function persist(Restaurant $restaurant): void
    {
        $this->getEntityManager()->persist($restaurant);
    }

    public function findActive(Uuid $id): ?Restaurant
    {
        return $this->findOneBy(['id' => $id, 'active' => true]);
    }
}
