<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

use Exception;

interface ErrorResponseFactoryInterface
{
    /**
     * @param int $status
     * @param null|string $title
     * @param null|string $detail
     * @param null|string $instance
     * @param string $type
     * @param array<string, mixed> $extensions
     * @return ErrorResponseInterface
     */
    public function createErrorResponse(
        int $status,
        ?string $title = null,
        ?string $detail = null,
        ?string $instance = null,
        string $type = 'about:blank',
        array $extensions = [],
    ): ErrorResponseInterface;
}
