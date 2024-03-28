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
use Moesif\MoesifBundle\Interfaces\EventSubscriberInterface as MoesifEventSubscriberInterface;

use Psr\Log\LoggerInterface;

class MoesifSubscriber implements SymfonyEventSubscriberInterface, MoesifEventSubscriberInterface
{
    private MoesifApiService $moesifApiService;
    private LoggerInterface $logger;

    public function __construct(MoesifApiService $moesifApiService, LoggerInterface $logger = null)
    {

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

      $this->logger->info(' hello in moesif subscriber on request ');
        // Optional: Perform actions before the request is handled, such as initializing logging or tracking
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
            'company_id' => $this->identifyCompanyId($request, $response),
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
        return null;
    }

    public function identifyCompanyId(Request $request, Response $response): ?string
    {
        return null;
    }

    public function identifySessionToken(Request $request, Response $response): ?string
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

    public function maskRequestBody(string $body): ?string
    {
        return $body;
    }

    public function maskResponseBody(string $body): ?string
    {
        return $body;
    }
}
