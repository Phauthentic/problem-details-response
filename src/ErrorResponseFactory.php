<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

use Exception;

class ErrorResponseFactory implements ErrorResponseFactoryInterface, ErrorResponseExceptionBasedFactoryInterface
{
    /**
     * @param array<int, callable> $callbacks
     * @return void
     */
    public function __construct(
        protected array $callbacks = [],
        protected bool $debug = false
    ) {
    }

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
    ): ErrorResponseInterface {
        return new ErrorResponse(
            status: $status,
            title: $title,
            detail: $detail,
            instance: $instance,
            type: $type,
            extensions: $extensions
        );
    }

    public function createErrorResponseFromException(Exception $exception): ErrorResponseInterface
    {
        foreach ($this->callbacks as $callback) {
            if (is_callable($callback)) {
                $result = $callback($exception);
                if ($result instanceof ErrorResponseInterface) {
                    return $result;
                }
            }
        }

        return new ErrorResponse(
            status: 500,
            title: $exception->getMessage(),
            extensions: $this->getExtensions($exception)
        );
    }

    /**
     * @param Exception $exception
     * @return array<string, mixed>
     */
    protected function getExtensions(Exception $exception): array
    {
        if ($this->debug === false) {
            return [];
        }

        return [
            'exception' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'exceptionTrace' => $exception->getTrace(),
        ];
    }
}
