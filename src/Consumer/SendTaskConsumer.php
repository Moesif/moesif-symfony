<?php

namespace Moesif\MoesifBundle\Consumer;

use Psr\Log\LoggerInterface;

abstract class SendTaskConsumer {
    /**
     * The Moesif application ID.
     */
    protected string $appId;

    /**
     * Optional configurations.
     */
    protected array $options;

    /**
     * Optional debug flag.
     */
    protected bool $debug;

    /**
     * Logger instance.
     */
    protected LoggerInterface $logger;

    /**
     * Creates a new SendTaskConsumer instance.
     *
     * @param string $applicationId The Moesif application ID.
     * @param array $options Additional options.
     */
    public function __construct(string $applicationId, LoggerInterface $logger, array $options = [], bool $debug = false) {
        $this->appId = $applicationId;
        $this->options = $options;
        $this->debug = $debug;
        $this->logger = $logger;
    }

    /**
     * Encode an array for persistence.
     *
     * @param array $params Parameters to encode.
     * @return string JSON encoded string.
     */
    protected function encode(array $params): string {
        return json_encode($params);
    }

    /**
     * Handle errors that occur during the consumer process.
     *
     * @param int $code Error code.
     * @param string $msg Error message.
     */
    protected function handleError(int $code, string $msg): void {
        if (isset($this->options['error_callback']) && is_callable($this->options['error_callback'])) {
            ($this->options['error_callback'])($code, $msg);
        }

        $this->log("Error [$code]: $msg");
    }

    /**
     * Log a message, if debugging is enabled.
     *
     * @param string $message The message to log.
     */
    protected function log(string $message): void {
        if ($this->debug) {
            // $this->logger->error($message);
        }
    }

    /**
     * Persist a batch of messages.
     *
     * @param array $batch An array of messages to consume.
     * @return bool Success or failure.
     */
    abstract public function persist(array $batch): bool;

    /**
     * Update user data.
     *
     * @param array $userData User data to update.
     * @return bool Success or failure.
     */
    abstract public function updateUser(array $userData): bool;

    /**
     * Update users in batch.
     *
     * @param array $usersBatchData Batch of user data to update.
     * @return bool Success or failure.
     */
    abstract public function updateUsersBatch(array $usersBatchData): bool;

    /**
     * Update company data.
     *
     * @param array $companyData Company data to update.
     * @return bool Success or failure.
     */
    abstract public function updateCompany(array $companyData): bool;

    /**
     * Update companies in batch.
     *
     * @param array $companiesBatchData Batch of company data to update.
     * @return bool Success or failure.
     */
    abstract public function updateCompaniesBatch(array $companiesBatchData): bool;

    /**
     * Update subscription data.
     * 
     * @param array $subscriptionData Subscription data to update.
     * @return bool Success or failure.
     */
    abstract public function updateSubscriptions(array $subscriptionData): bool;

    /**
     * Update subscriptions in batch.
     * 
     * @param array $subscriptionsBatchData Batch of subscription data to update.
     * @return bool Success or failure.
     */
    abstract public function updateSubscriptionsBatch(array $subscriptionsBatchData): bool;
}
