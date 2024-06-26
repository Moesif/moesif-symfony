<?php

namespace Moesif\MoesifBundle\EventSubscriber;

use DateTime;
use DateTimeZone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as SymfonyEventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Moesif\MoesifBundle\Service\MoesifApiService;
use Moesif\MoesifBundle\Interfaces\MoesifHooksInterface;

use Psr\Log\LoggerInterface;

class MoesifSubscriber implements SymfonyEventSubscriberInterface
{
    private MoesifApiService $moesifApiService;
    private LoggerInterface $logger;
    private $options;

    private $configHooks;

    public function __construct(MoesifApiService $moesifApiService, MoesifHooksInterface $configHooks, LoggerInterface $logger = null)
    {
        $this->configHooks = $configHooks;
        $this->logger = $logger;
        $this->moesifApiService = $moesifApiService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
            KernelEvents::RESPONSE => ['onKernelResponse', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $startTime = new DateTime();
        $startTime->setTimezone(new DateTimeZone("UTC"));

        $request = $event->getRequest();
        $request->attributes->set('mo_start_time', $startTime);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $data = $this->prepareData($request, $response);
        if ($data) {
            $this->moesifApiService->track($data);
        }
    }

    private function prepareData(Request $request, Response $response): array
    {
        $startTime = $request->attributes->get('mo_start_time');

        $endTime = new DateTime();
        $endTime->setTimezone(new DateTimeZone("UTC"));

        $requestData = [
            'time' => $startTime->format('Y-m-d\TH:i:s.uP'),
            'verb' => $request->getMethod(),
            'uri' => $request->getUri(),
            'ip_address' => $this->getIp($request),
            'headers' => $this->maskRequestHeaders($request->headers->all()),
            'body' => $this->maskRequestBody($request->getContent()), // Assuming JSON content
            'transfer_encoding' => 'json',
        ];

        $responseData = [
            'time' => $endTime->format('Y-m-d\TH:i:s.uP'),
            'status' => $response->getStatusCode(),
            'headers' => $this->maskResponseHeaders($response->headers->all()),
            'body' => $this->maskResponseBody($response->getContent()), // Assuming JSON content
            'transfer_encoding' => 'json',
        ];

        $eventModel = [
            'request' => $requestData,
            'response' => $responseData,
            'user_id' => $this->identifyUserId($request, $response),
            'company_id' => $this->identifyCompanyId($request, $response),
            'session_token' => $this->identifySessionToken($request, $response),
            'metadata' => $this->getMetadata($request, $response),
        ];

        return $eventModel;
    }

    private function getIp(Request $request): ?string
    {
        $headers = ['X-Client-IP', 'CF-Connecting-IP', 'X-Forwarded-For', 'X-Forwarded', 'True-Client-IP', 'X-Real-IP', 'X-Cluster-Client-IP', 'Forwarded-For', 'Forwarded', 'Remote-Addr'];
        foreach ($headers as $header) {
            $ip = $request->headers->get($header);
            if ($ip) {
                // Optionally, validate the IP address
                return $ip;
            }
        }

        return $request->getClientIp();
    }

    public function identifyUserId(Request $request, Response $response): ?string
    {

        if ($this->configHooks) {
          return $this->configHooks->identifyUserId($request, $response);
        }
        return null;
    }

    public function identifyCompanyId(Request $request, Response $response): ?string
    {
        if ($this->configHooks) {
          return $this->configHooks->identifyCompanyId($request, $response);
        }
        return null;
    }

    public function identifySessionToken(Request $request, Response $response): ?string
    {
        if ($this->configHooks) {
          return $this->configHooks->identifySessionToken($request, $response);
        }
        return null;
    }

    public function getMetadata(Request $request, Response $response): ?array
    {
        if ($this->configHooks) {
          return $this->configHooks->getMetadata($request, $response);
        }
        return null;
    }

    public function skip(Request $request, Response $response): bool
    {
        if ($this->configHooks) {
          return $this->configHooks->skip($request, $response);
        }
        return false;
    }

    public function maskRequestHeaders(array $headers): array
    {
        if ($this->configHooks) {
          return $this->configHooks->maskRequestHeaders($headers);
        }
        return $headers;
    }

    public function maskResponseHeaders(array $headers): array
    {
        if ($this->configHooks) {
          return $this->configHooks->maskResponseHeaders($headers);
        }
        return $headers;
    }

    public function maskRequestBody($body)
    {
        if ($this->configHooks) {
          return $this->configHooks->maskRequestBody($body);
        }
        return $body;
    }

    public function maskResponseBody($body)
    {
        if ($this->configHooks) {
          return $this->configHooks->maskResponseBody($body);
        }
        return $body;
    }
}
