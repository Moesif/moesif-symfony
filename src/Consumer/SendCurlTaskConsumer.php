<?php

namespace Moesif\MoesifBundle\Consumer;

use Exception;
use Psr\Log\LoggerInterface;

class SendCurlTaskConsumer {
    protected string $host;
    protected string $endpoint;
    protected string $usersEndpoint;
    protected string $usersBatchEndpoint;
    protected string $companyEndpoint;
    protected string $companiesBatchEndpoint;
    protected string $subscriptionEndpoint;
    protected string $subscriptionsBatchEndpoint;
    protected int $connectTimeout;
    protected int $timeout;
    protected string $protocol;
    protected bool $fork;
    protected string $appId;
    protected bool $debug;
    protected LoggerInterface $logger;

    public function __construct(string $appId, LoggerInterface $logger, array $options = [], bool $debug = false) {
        $this->appId = $appId;
        $this->host = $options['host'] ?? 'api.moesif.net';
        $this->endpoint = $options['endpoint'] ?? '/v1/events/batch';
        $this->usersEndpoint = $options['users_endpoint'] ?? '/v1/users';
        $this->usersBatchEndpoint = $options['users_batch_endpoint'] ?? '/v1/users/batch';
        $this->companyEndpoint = $options['company_endpoint'] ?? '/v1/companies';
        $this->companiesBatchEndpoint = $options['companies_batch_endpoint'] ?? '/v1/companies/batch';
        $this->subscriptionEndpoint = $options['subscription_endpoint'] ?? '/v1/subscriptions';
        $this->subscriptionsBatchEndpoint = $options['subscriptions_batch_endpoint'] ?? '/v1/subscriptions/batch';
        $this->connectTimeout = $options['connect_timeout'] ?? 5;
        $this->timeout = $options['timeout'] ?? 30;
        $this->protocol = $options['use_ssl'] ?? true ? 'https' : 'http';
        $this->fork = $options['fork'] ?? false;
        $this->debug = $debug;
        $this->logger = $logger;

        if ($this->fork && !function_exists('exec')) {
            if ($this->debug) {
                // $this->logger->error('The "exec" function must be enabled to use the cURL consumer in "fork" mode.');
            }
            throw new Exception('The "exec" function must be enabled to use the cURL consumer in "fork" mode.');
        }
        if (!$this->fork && !function_exists('curl_init')) {
            if ($this->debug) {
                // $this->logger->error('The cURL PHP extension is required to use the cURL consumer with fork = false.');
            }
            throw new Exception('The cURL PHP extension is required to use the cURL consumer with fork = false.');
        }
    }

    public function persist(array $batch): bool {
        if (empty($batch)) {
            return true;
        }

        $data = json_encode($batch);
        $url = $this->protocol . '://' . $this->host . $this->endpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateUser(array $userData): bool {
        $data = json_encode($userData);
        $url = $this->protocol . '://' . $this->host . $this->usersEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateUsersBatch(array $usersBatchData): bool {
        $data = json_encode($usersBatchData);
        $url = $this->protocol . '://' . $this->host . $this->usersBatchEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateCompany(array $companyData): bool {
        $data = json_encode($companyData);
        $url = $this->protocol . '://' . $this->host . $this->companyEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateCompaniesBatch(array $companiesBatchData): bool {
        $data = json_encode($companiesBatchData);
        $url = $this->protocol . '://' . $this->host . $this->companiesBatchEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateSubscription(array $subscriptionData): bool {
        $data = json_encode($subscriptionData);
        $url = $this->protocol . '://' . $this->host . $this->subscriptionEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    public function updateSubscriptionsBatch(array $subscriptionsBatchData): bool {
        $data = json_encode($subscriptionsBatchData);
        $url = $this->protocol . '://' . $this->host . $this->subscriptionsBatchEndpoint;

        return $this->fork ? $this->_executeForked($url, $data) : $this->_executeCurl($url, $data);
    }

    protected function _executeCurl(string $url, string $data): bool {

        if ($this->debug) {
            // $this->logger->error('Moesif cURL data: ' . $data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Moesif-Application-Id: ' . $this->appId
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $curlCommand = 'curl -X POST -H "Content-Type: application/json" -H "X-Moesif-Application-Id: ' . $this->appId . '" -d \'' . addslashes($data) . '\' \'' . $url . '\'';
        if ($this->debug) {
            $this->logger->info('Moesif cURL command: ' . $curlCommand);
        }

        $result = curl_exec($ch);

        if ($this->debug) {
            $this->logger->error('Moesif cURL result: ' . $result);
        }

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            if ($this->debug) {
                $this->logger->error('Moesif cURL consumer error: ' . $error);
            }
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return true;
    }

    protected function _executeForked(string $url, string $data): bool {
        //$escapedData = escapeshellarg($data);
        $command = "curl -X POST -H 'Content-Type: application/json' -H 'X-Moesif-Application-Id: {$this->appId}' -d {$data} '{$url}' > /dev/null 2>&1 &";

        exec($command, $output, $returnVar);
        return $returnVar === 0;
    }
}
