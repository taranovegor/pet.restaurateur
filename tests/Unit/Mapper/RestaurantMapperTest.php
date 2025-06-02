<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Tests\Unit\Mapper;

use App\Dto\Restaurant\Embedded\WorkingHoursDto;
use App\Dto\Restaurant\RestaurantUpdateDto;
use App\Entity\Restaurant\Restaurant;
use App\Entity\Restaurant\WorkingHours;
use App\Mapper\RestaurantMapper;
use App\Mapper\WorkingHoursMapper;
use App\ValueObject\Coordinates;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

final class RestaurantMapperTest extends TestCase
{
    private RestaurantMapper $mapper;
    private WorkingHoursMapper $workingHoursMapper;

    protected function setUp(): void
    {
        $this->workingHoursMapper = $this->createMock(WorkingHoursMapper::class);
        $this->mapper = new RestaurantMapper($this->workingHoursMapper);
    }

    public function testItUpdatesAllFieldsFromDto(): void
    {
        $restaurant = new Restaurant('Old Name', 'Old Description', 'Old Address', new Coordinates(0, 0), 'Old Phone');

        $updateDto = new RestaurantUpdateDto(
            'New Description',
            'New Address',
            new Coordinates(1, 1),
            'New Phone',
            [$this->createWorkingHoursDto(1)]
        );

        $expectedWH = $this->createWorkingHours($restaurant, 1);

        $this->workingHoursMapper->expects($this->once())
            ->method('mapDtoToEntityForRestaurant')
            ->with($restaurant, $updateDto->workingHours[0])
            ->willReturn($expectedWH);

        $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);

        $this->assertEquals('New Description', $restaurant->getDescription());
        $this->assertEquals('New Address', $restaurant->getAddress());
        $this->assertEquals(new Coordinates(1, 1), $restaurant->getCoordinates());
        $this->assertEquals('New Phone', $restaurant->getPhone());
        $this->assertCount(1, $restaurant->getWorkingHours());
    }

    public function testItUpdatesExistingWorkingHours(): void
    {
        $restaurant = $this->createRestaurant();
        $existing = $this->createWorkingHours($restaurant, 1);
        $restaurant->addWorkingHours($existing);

        $dto = $this->createWorkingHoursDto(1);

        $updateDto = new RestaurantUpdateDto(
            'Desc', 'Addr', new Coordinates(0, 0), 'Phone', [$dto]
        );

        $this->workingHoursMapper->expects($this->once())
            ->method('mapToEntity')
            ->with($existing, $dto);

        $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);
    }

    public function testItAddsNewWorkingHoursWhenNotExists(): void
    {
        $restaurant = $this->createRestaurant();
        $dto = $this->createWorkingHoursDto(2);
        $expected = $this->createWorkingHours($restaurant, 2);

        $updateDto = new RestaurantUpdateDto(
            'Desc', 'Addr', new Coordinates(0, 0), 'Phone', [$dto]
        );

        $this->workingHoursMapper->expects($this->once())
            ->method('mapDtoToEntityForRestaurant')
            ->with($restaurant, $dto)
            ->willReturn($expected);

        $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);

        $this->assertCount(1, $restaurant->getWorkingHours());
        $this->assertEquals($expected, $restaurant->findWorkingHours(2));
    }

    public function testItRemovesAllWorkingHoursWhenDtoIsEmpty(): void
    {
        $restaurant = $this->createRestaurant();
        $existing = $this->createWorkingHours($restaurant, 1);
        $restaurant->addWorkingHours($existing);

        $updateDto = new RestaurantUpdateDto(
            'Desc', 'Addr', new Coordinates(0, 0), 'Phone', []
        );

        $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);

        $this->assertCount(0, $restaurant->getWorkingHours());
    }

    public function testItHandlesMixOfExistingAndNewWorkingHours(): void
    {
        $restaurant = $this->createRestaurant();

        $existingWh = $this->createWorkingHours($restaurant, 1);
        $restaurant->addWorkingHours($existingWh);

        $dto1 = $this->createWorkingHoursDto(1);
        $dto2 = new WorkingHoursDto(2, new DateTimeImmutable('08:00'), new DateTimeImmutable('17:00'));

        $newWh = new WorkingHours(
            $restaurant,
            2,
            new DateTimeImmutable('08:00'),
            new DateTimeImmutable('17:00')
        );

        $updateDto = new RestaurantUpdateDto(
            'Desc', 'Addr', new Coordinates(0, 0), 'Phone', [$dto1, $dto2]
        );

        $this->workingHoursMapper->expects($this->once())
            ->method('mapToEntity')
            ->with($existingWh, $dto1);

        $this->workingHoursMapper->expects($this->once())
            ->method('mapDtoToEntityForRestaurant')
            ->with($restaurant, $dto2)
            ->willReturn($newWh);

        $this->mapper->mapUpdateDtoToEntity($updateDto, $restaurant);

        $this->assertCount(2, $restaurant->getWorkingHours());
        $this->assertNotNull($restaurant->findWorkingHours(1));
        $this->assertNotNull($restaurant->findWorkingHours(2));
    }

    // === ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ===

    private function createRestaurant(): Restaurant
    {
        return new Restaurant('Name', 'Desc', 'Addr', new Coordinates(0, 0), 'Phone');
    }

    private function createWorkingHoursDto(int $day): WorkingHoursDto
    {
        return new WorkingHoursDto(
            $day,
            new DateTimeImmutable('10:00'),
            new DateTimeImmutable('19:00')
        );
    }

    private function createWorkingHours(Restaurant $restaurant, int $day): WorkingHours
    {
        return new WorkingHours(
            $restaurant,
            $day,
            new DateTimeImmutable('10:00'),
            new DateTimeImmutable('19:00')
        );
    }
}
