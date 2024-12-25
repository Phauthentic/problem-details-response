<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails\Tests;

use Exception;
use Phauthentic\ProblemDetails\ErrorResponse;
use Phauthentic\ProblemDetails\ErrorResponseFactory;
use Phauthentic\ProblemDetails\ErrorResponseInterface;
use PHPUnit\Framework\TestCase;

class ErrorResponseFactoryTest extends TestCase
{
    public function testCreateErrorResponseFromExceptionWithCallback(): void
    {
        $callback = function (Exception $exception) {
            return new ErrorResponse(404, 'Not Found', 'Resource not found');
        };

        $factory = new ErrorResponseFactory([$callback]);
        $exception = new Exception('Test Exception');

        $result = $factory->createErrorResponseFromException($exception);

        $this->assertInstanceOf(ErrorResponseInterface::class, $result);
        $this->assertSame(404, $result->getStatus());
        $this->assertSame('Not Found', $result->getTitle());
        $this->assertSame('Resource not found', $result->getDetail());
    }

    public function testCreateErrorResponseFromExceptionWithDefault(): void
    {
        $factory = new ErrorResponseFactory();
        $exception = new Exception('Test Exception');

        $result = $factory->createErrorResponseFromException($exception);

        $this->assertInstanceOf(ErrorResponseInterface::class, $result);
        $this->assertSame(500, $result->getStatus());
        $this->assertSame('Test Exception', $result->getTitle());
    }

    public function testCreateErrorResponseFromExceptionWithCustomCallback(): void
    {
        $customCallback = function (Exception $exception) {
            return new ErrorResponse(403, 'Forbidden', 'Access Denied');
        };

        $factory = new ErrorResponseFactory([$customCallback]);
        $exception = new Exception('Custom Exception');

        $result = $factory->createErrorResponseFromException($exception);

        $this->assertInstanceOf(ErrorResponseInterface::class, $result);
        $this->assertSame(403, $result->getStatus());
        $this->assertSame('Forbidden', $result->getTitle());
        $this->assertSame('Access Denied', $result->getDetail());
    }
}
