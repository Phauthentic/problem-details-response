<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails\Tests;

use Exception;
use Nyholm\Psr7\Factory\Psr17Factory;
use Phauthentic\ProblemDetails\ErrorResponseFactory;
use Phauthentic\ProblemDetails\ErrorResponseMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorResponseMiddlewareTest extends TestCase
{
    public function testThatExceptionInHandlerWillReturnErrorResponse(): void
    {
        $middleware = new ErrorResponseMiddleware(
            new Psr17Factory(),
            new ErrorResponseFactory(),
            [Exception::class]
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException(new Exception('Handled Exception'));

        $response = $middleware->process($request, $handler);

        $this->assertStringContainsString('Handled Exception', (string)$response->getBody());
        $this->assertSame(
            '{"type":"about:blank","status":500,"title":"Handled Exception","detail":null,"instance":null}',
            (string)$response->getBody()
        );
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('application/problem+json', $response->getHeaderLine('Content-Type'));
    }

    public function testProcessHandlesUnhandledException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unhandled Exception');

        $middleware = new ErrorResponseMiddleware(
            new Psr17Factory(),
            new ErrorResponseFactory(),
            [CustomException::class]
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $unhandledException = new Exception('Unhandled Exception');

        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException($unhandledException);

        $middleware->process($request, $handler);
    }
}
