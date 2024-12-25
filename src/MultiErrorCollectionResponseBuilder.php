<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

use InvalidArgumentException;

/**
 * This class merges multiple error messages into one.
 */
class MultiErrorCollectionResponseBuilder
{
    public function __construct(
        protected ErrorResponseFactoryInterface $errorResponseFactory
    ) {
    }

    /**
     * @var array<int, ErrorResponseInterface>
     */
    protected array $responses = [];

    public function assertSameType(ErrorResponseInterface $errorResponse): void
    {
        if (!$this->responses[0]->isSameType($errorResponse)) {
            throw new InvalidArgumentException(sprintf(
                'All responses must be of the same type: %s',
                $this->responses[0]->getType()
            ));
        }
    }

    public function add(ErrorResponseInterface $errorResponse): void
    {
        if (isset($this->responses[0])) {
            $this->assertSameType($errorResponse);
        }

        $this->responses[] = $errorResponse;
    }

    public function getResponse(): ErrorResponseInterface
    {
        $extensions = [];
        foreach ($this->responses as $response) {
            $extensions['errors'][] = array_merge([
                'detail' => $response->getDetail(),
                'instance' => $response->getInstance(),
            ], $response->getExtensions());
        }

        $errorResponse = $this->errorResponseFactory->createErrorResponse(
            status: $this->responses[0]->getStatus(),
            title: $this->responses[0]->getTitle(),
            type: $this->responses[0]->getType(),
            extensions: $extensions
        );

        $this->responses = [];

        return $errorResponse;
    }
}
