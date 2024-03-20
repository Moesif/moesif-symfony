<?php

namespace Moesif\MoesifBundle\EventSubscriber;

use DateTime;
use DateTimeZone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Moesif\MoesifBundle\Service\MoesifApiService;

class MoesifSubscriber implements EventSubscriberInterface
{
    private MoesifApiService $moesifApiService;

    public function __construct(MoesifApiService $moesifApiService)
    {
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

    private function prepareData(Request $request, $response): array
    {
        $startTime = new DateTime();
        $startTime->setTimezone(new DateTimeZone("UTC"));
        
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

    private function identifyUserId(Request $request, $response): ?string
    {
        return null;
    }

    private function identifyCompanyId(Request $request, $response): ?string
    {
        return null;
    }

    private function identifySessionToken(Request $request, $response): ?string
    {
        return null;
    }

    private function getMetadata(Request $request, $response): ?array
    {
        return null;
    }

    private function skip(Request $request, $response): bool
    {
        return false;
    }

    private function maskRequestHeaders(array $headers): array
    {
        return $headers;
    }

    private function maskResponseHeaders(array $headers): array
    {
        return $headers;
    }

    private function maskRequestBody(string $body): ?string
    {
        return $body;
    }

    private function maskResponseBody(string $body): ?string
    {
        return $body;
    }
}
