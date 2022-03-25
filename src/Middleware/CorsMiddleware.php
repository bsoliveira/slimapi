<?php

declare(strict_types=1);

namespace App\Middleware;

use Neomerx\Cors\Analyzer;
use Psr\Log\LoggerInterface;
use Neomerx\Cors\Strategies\Settings;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Neomerx\Cors\Contracts\AnalysisResultInterface;

class CorsMiddleware
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var Analyzer
     */
    private $analyzer;

    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param string $serverOriginScheme
     * @param string $serverOriginHost
     * @param integer $serverOriginPort
     * @param boolean $checkHost
     * @param integer $cacheMaxAge
     * @param array $allowedOrigins
     * @param array $allowedMethods
     * @param array $allowedHeaders
     * @param array $exposedHeaders
     * @param boolean $isUseCredentials
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        string $serverOriginScheme,
        string $serverOriginHost,
        int $serverOriginPort,
        bool $checkHost,
        int $cacheMaxAge,
        array $allowedOrigins,
        array $allowedMethods,
        array $allowedHeaders,
        array $exposedHeaders,
        bool $isUseCredentials,
        ?LoggerInterface $logger
    ) {

        $this->responseFactory = $responseFactory;

        $settings = new Settings();

        $settings->setServerOrigin($serverOriginScheme, $serverOriginHost, $serverOriginPort);

        if ($checkHost) {
            $settings->enableCheckHost();
        } else {
            $settings->disableCheckHost();
        }

        $settings->setPreFlightCacheMaxAge($cacheMaxAge);

        if ($allowedOrigins == ['*']) {
            $settings->enableAllOriginsAllowed();
        } else {
            $settings->setAllowedOrigins($allowedOrigins);
        }

        if ($allowedMethods == ['*']) {
            $settings->enableAllMethodsAllowed();
        } else {
            $settings->setAllowedMethods($allowedMethods);
        }

        if ($allowedHeaders == ['*']) {
            $settings->enableAllHeadersAllowed();
        } else {
            $settings->setAllowedHeaders($allowedHeaders);
        }

        $settings->setExposedHeaders($exposedHeaders);

        if ($isUseCredentials) {
            $settings->setCredentialsSupported();
        } else {
            $settings->setCredentialsNotSupported();
        }

        $settings->enableAddAllowedMethodsToPreFlightResponse();
        $settings->enableAddAllowedHeadersToPreFlightResponse();

        $analyzer = Analyzer::instance($settings);

        if ($logger) {
            $analyzer->setLogger($logger);
        }

        $this->analyzer = $analyzer;
    }

    /**
     * Analyze CORS
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * 
     * @throws HttpForbiddenException
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cors = $this->analyzer->analyze($request);

        switch ($cors->getRequestType()) {
            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
                throw new HttpForbiddenException($request, "No host header");
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
                throw new HttpForbiddenException($request, "Origin not allowed");
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
                throw new HttpForbiddenException($request, "Method not supported");
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
                throw new HttpForbiddenException($request, "Headers not supported");
            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                // return 200 HTTP with $corsHeaders
                $response = $this->responseFactory->createResponse(200);
                $response = $this->withCorsHeaders($response, $cors);

                return $response;
            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                // call next middleware handler
                return $handler->handle($request);
            default:
                // actual CORS request with $corsHeaders
                $response = $handler->handle($request);
                $response = $this->withCorsHeaders($response, $cors);

                return $response;
        }
    }

    /**
     * Adds cors headers to the response.
     */
    protected function withCorsHeaders(ResponseInterface $response, AnalysisResultInterface $cors): ResponseInterface
    {
        $corsHeaders = $cors->getResponseHeaders();

        foreach ($corsHeaders as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
