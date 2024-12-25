<?php

declare(strict_types=1);

namespace Phauthentic\ProblemDetails;

use Exception;
use JsonException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *
 */
class ErrorResponseMiddleware implements MiddlewareInterface
{
    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param ErrorResponseExceptionBasedFactoryInterface $errorResponseFactory
     * @param array<int, string> $exceptionClasses
     * @param bool $onlyOnJsonRequests
     */
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected ErrorResponseExceptionBasedFactoryInterface $errorResponseFactory,
        protected array $exceptionClasses = [Exception::class],
        protected bool $onlyOnJsonRequests = true
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!$this->isJsonRequest($request)) {
            return $handler->handle($request);
        }

        try {
            return $handler->handle($request);
        } catch (Exception $exception) {
            if ($this->isAnInterceptableException($exception)) {
                return $this->createResponse(
                    $this->errorResponseFactory->createErrorResponseFromException($exception)
                );
            }

            throw $exception;
        }
    }

    protected function isJsonRequest(ServerRequestInterface $request): bool
    {
        return $this->onlyOnJsonRequests
            && in_array('application/json', $request->getHeader('Accept'), true);
    }

    protected function isAnInterceptableException(Exception $exception): bool
    {
        foreach ($this->exceptionClasses as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ErrorResponseInterface $errorResponse
     * @return string
     * @throws JsonException
     */
    protected function errorResponseToJson(ErrorResponseInterface $errorResponse): string
    {
        return json_encode($errorResponse->toArray(), JSON_THROW_ON_ERROR);
    }

    public function createResponse(ErrorResponseInterface $errorResponse): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($errorResponse->getStatus());

        $body = $response->getBody();
        $body->write($this->errorResponseToJson($errorResponse));

        return $response
            ->withStatus($errorResponse->getStatus())
            ->withBody($body)
            ->withHeader('Content-Type', 'application/problem+json');
    }
}
