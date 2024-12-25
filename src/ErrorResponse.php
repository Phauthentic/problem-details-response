<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

use InvalidArgumentException;

class ErrorResponse implements ErrorResponseInterface
{
    /**
     * @param int $status
     * @param string|null $title
     * @param string|null $detail
     * @param string|null $instance
     * @param string $type
     * @param array<string, mixed> $extensions
     */
    public function __construct(
        protected int $status,
        protected ?string $title = null,
        protected ?string $detail = null,
        protected ?string $instance = null,
        protected string $type = 'about:blank',
        protected array $extensions = [],
    ) {
        $this->assertValidHttpStatusCode($status);
        $this->assertProtectedKeysAreNotInThExtensions($extensions);
    }

    protected function assertValidHttpStatusCode(int $statusCode): void
    {
        if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode > 599) {
            throw new InvalidArgumentException(sprintf(
                'Invalid HTTP status code: %d',
                $statusCode
            ));
        }
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getInstance(): ?string
    {
        return $this->instance;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function isSameType(ErrorResponseInterface $errorResponse): bool
    {
        return $this->type === $errorResponse->getType();
    }

    /**
     * @param array<string, mixed> $array
     * @return void
     */
    protected function assertProtectedKeysAreNotInThExtensions(array $array): void
    {
        $keysToCheck = ['type', 'status', 'title', 'detail', 'instance'];

        foreach ($keysToCheck as $key) {
            assert(array_key_exists($key, $array), "The protected key '$key' is not allowed in the extensions.");
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'status' => $this->status,
            'title' => $this->title,
            'detail' => $this->detail,
            'instance' => $this->instance,
        ];

        foreach ($this->extensions as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
