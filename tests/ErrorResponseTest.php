<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails\Tests;

use InvalidArgumentException;
use Phauthentic\ProblemDetails\ErrorResponse;
use PHPUnit\Framework\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testValidHttpStatus(): void
    {
        $errorResponse = new ErrorResponse(200);

        $this->assertSame(200, $errorResponse->getStatus());
        $this->assertSame('about:blank', $errorResponse->getType());
        $this->assertNull($errorResponse->getTitle());
        $this->assertNull($errorResponse->getDetail());
        $this->assertNull($errorResponse->getInstance());
    }

    public function testInvalidHttpStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code: 700');

        new ErrorResponse(700);
    }

    public function testToArray(): void
    {
        $errorResponse = new ErrorResponse(
            status: 500,
            title: 'Internal Server Error',
            detail: 'Something went wrong',
            instance: null,
            type: 'https://example.com/server-error',
            extensions: ['extra' => 'data']
        );

        $expectedArray = [
            'type' => 'https://example.com/server-error',
            'status' => 500,
            'title' => 'Internal Server Error',
            'detail' => 'Something went wrong',
            'instance' => null,
            'extra' => 'data',
        ];

        $this->assertSame($expectedArray, $errorResponse->toArray());
    }

    public function testIsSameType(): void
    {
        $errorResponse1 = new ErrorResponse(
            status: 400,
            title: 'Bad Request',
            detail: 'Invalid input',
            type: 'https://example.com/bad-request',
        );

        $errorResponse2 = new ErrorResponse(
            status: 400,
            title: 'Bad Request',
            detail: 'Invalid input',
            type: 'https://example.com/bad-request',
        );

        $this->assertTrue($errorResponse1->isSameType($errorResponse2));
    }
}
