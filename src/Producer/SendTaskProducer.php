<?php

namespace Moesif\MoesifBundle\Producer;

use Exception;
use Moesif\MoesifBundle\Consumer\SendCurlTaskConsumer;
use Psr\Log\LoggerInterface;

class SendTaskProducer {
    /**
     * @var string a token associated to a Moesif project
     */
    protected $appId;

    /**
     * debug flag
     */
    protected $debug;

    /**
     * @var array a queue to hold messages in memory before flushing in batches
     */
    private $queue = [];

    /**
     * @var SendCurlTaskConsumer the consumer to use when flushing messages
     */
    private $consumer;

    /**
     * If the queue reaches this size, we'll auto-flush to prevent out of memory errors.
     * @var int
     */
    protected $maxQueueSize = 5;

    /**
     * @var LoggerInterface
     * The logger to use for debugging and error logging
     */
    private $logger;

    public function __construct(string $appId, LoggerInterface $logger, array $options = [], bool $debug = false) {
        $this->appId = $appId;
        $this->consumer = new SendCurlTaskConsumer($this->appId, $logger, $options, $debug);
        $this->maxQueueSize = $options['max_queue_size'] ?? $this->maxQueueSize;
        $this->debug = $debug;
        $this->logger = $logger;
        $this->logger->info("SendTaskProducer instantiated.");
    }

    public function __destruct() {
        $this->flush();
    }

    public function flush(int $desiredBatchSize = 10): bool {
        $queueSize = count($this->queue);
        $succeeded = true;

        while ($queueSize > 0 && $succeeded) {
            $batchSize = min($queueSize, $desiredBatchSize);
            $batch = array_splice($this->queue, 0, $batchSize);
            $succeeded = $this->consumer->persist($batch);

            if (!$succeeded) {
                // If batch consumption failed, add batch back to queue
                $this->queue = array_merge($batch, $this->queue);
            }

            $queueSize = count($this->queue);
        }

        return $succeeded;
    }

    public function reset() {
        $this->queue = [];
    }

    public function enqueue(array $message = []) {
        $this->queue[] = $message;

        if (count($this->queue) > $this->maxQueueSize) {
            $this->flush();
        }
    }

    public function enqueueAll(array $messages = []) {
        foreach ($messages as $message) {
            $this->enqueue($message);
        }
    }

    // User and Company update methods assume the existence of such methods in SendCurlTaskConsumer
    public function updateUser(array $userData) {
        return $this->consumer->updateUser($userData);
    }

    public function updateUsersBatch(array $usersBatchData) {
        return $this->consumer->updateUsersBatch($usersBatchData);
    }

    public function updateCompany(array $companyData) {
        return $this->consumer->updateCompany($companyData);
    }

    public function updateCompaniesBatch(array $companiesBatchData) {
        return $this->consumer->updateCompaniesBatch($companiesBatchData);
    }

    public function track(array $data) {
        $this->logger->info("Enqueuing Moesif event.", $data);
        $this->enqueue($data);
    }
}
