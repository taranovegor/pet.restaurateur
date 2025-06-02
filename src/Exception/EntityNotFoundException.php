<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityNotFoundException extends NotFoundHttpException
{
    private ?string $className;
    private string|array|null $id;

    public function __construct(
        string $message = '',
        ?string $className = null,
        string|array|null $id = null,
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct($message, $previous, $code, $headers);
        $this->className = $className;
        $this->id = $id;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getId(): array|int
    {
        return $this->id;
    }
}
