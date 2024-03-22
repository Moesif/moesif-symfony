<?php

namespace Moesif\MoesifBundle\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface EventSubscriberInterface {
    public function identifyUserId(Request $request, Response $response): ?string;
    public function identifyCompanyId(Request $request, Response $response): ?string;
    public function identifySessionToken(Request $request, Response $response): ?string;
    public function getMetadata(Request $request, Response $response): ?array;
    public function skip(Request $request, Response $response): bool;
    public function maskRequestHeaders(array $headers): array;
    public function maskResponseHeaders(array $headers): array;
    public function maskRequestBody(string $body): ?string;
    public function maskResponseBody(string $body): ?string;
}