<?php

namespace Moesif\MoesifBundle\Interfaces;

use Moesif\MoesifBundle\Interfaces\MoesifHooksInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MoesifDefaultHooks implements MoesifHooksInterface {

  public function __construct() {
  }

  public function identifyUserId(Request $request, Response $response): string|null
  {
    return null;
  }

  public function identifyCompanyId(Request $request, Response $response): string|null
  {
    return null;
  }

  public function identifySessionToken(Request $request, Response $response): string|null
  {
    return null;
  }

  public function getMetadata(Request $request, Response $response): ?array
  {
      return null;
  }

  public function skip(Request $request, Response $response): bool
  {
      return false;
  }

  public function maskRequestHeaders(array $headers): array
  {
      return $headers;
  }

  public function maskResponseHeaders(array $headers): array
  {
      return $headers;
  }

  public function maskRequestBody($body)
  {
      return $body;
  }

  public function maskResponseBody($body)
  {
      return $body;
  }
}
