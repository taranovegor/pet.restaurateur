<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Doctrine\Type;

use App\ValueObject\Coordinates;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class CoordinatesType extends Type
{
    public function getName(): string
    {
        return CoordinatesType::lookupName($this);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'POINT';
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['point'];
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Coordinates) {
            return sprintf('(%F,%F)', $value->getLatitude(), $value->getLongitude());
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            CoordinatesType::lookupName($this),
            ['null', Coordinates::class],
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Coordinates
    {
        if (null === $value || $value instanceof Coordinates) {
            return $value;
        }

        list($latitude, $longitude) = sscanf($value, '(%f,%f)');

        return new Coordinates($latitude, $longitude);
    }
}
