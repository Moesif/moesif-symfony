<?php

namespace Moesif\MoesifBundle\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Moesif\MoesifBundle\Producer\SendTaskProducer;

class MoesifApiService {

    private $sendProducer;
    private $logger;

    /**
     * Instantiates a new MoesifApi instance.
     * The SendTaskProducer dependency is injected via the service container.
     */
    public function __construct(SendTaskProducer $sendProducer, LoggerInterface $logger) {
        $this->sendProducer = $sendProducer;
        $this->logger = $logger;
        // $this->logger->info("MoesifApiService instantiated.");
    }

    /**
     * Add an array representing a message to be sent to Moesif to the in-memory queue.
     */
    public function enqueue(array $message = []) {
        // $this->logger->info("Enqueuing message to Moesif.", $message);
        $this->sendProducer->enqueue($message);
    }

    /**
     * Add an array representing a list of messages to be sent to Moesif to a queue.
     */
    public function enqueueAll(array $messages = []) {
        // $this->logger->info("Enqueuing multiple messages to Moesif.", $messages);
        $this->sendProducer->enqueueAll($messages);
    }

    /**
     * Flush the events queue.
     */
    public function flush(int $desiredBatchSize = 10) {
        // $this->logger->info("Flushing Moesif events queue.", ['batch_size' => $desiredBatchSize]);
        $this->sendProducer->flush($desiredBatchSize);
    }

    /**
     * Updates a user in Moesif.
     */
    public function updateUser(array $userData) {
        // $this->logger->info("Updating user in Moesif.", $userData);
        if (empty($userData) || !isset($userData['user_id'])) {
            // $this->logger->error("Moesif updateUser requires user_id field to be set and not empty.");
            throw new Exception('Moesif updateUser requires user_id field to be set and not empty.');
        }

        $this->sendProducer->updateUser($userData);
    }

    /**
     * Updates users in batch in Moesif.
     */
    public function updateUsersBatch(array $usersBatchData = []) {
        // $this->logger->info("Updating users in batch in Moesif.", $usersBatchData);
        foreach ($usersBatchData as $userData) {
            if (empty($userData) || !isset($userData['user_id'])) {
                // $this->logger->error("Each userData in updateUsersBatch requires a user_id field to be set and not empty.", $userData);
                throw new Exception('Each userData in updateUsersBatch requires a user_id field to be set and not empty.');
            }
        }

        $this->sendProducer->updateUsersBatch($usersBatchData);
    }

    /**
     * Updates a company in Moesif.
     */
    public function updateCompany(array $companyData) {
        // $this->logger->info("Updating company in Moesif.", $companyData);
        if (empty($companyData) || !isset($companyData['company_id'])) {
            // $this->logger->error("Moesif updateCompany requires company_id field to be set and not empty.");
            throw new Exception('Moesif updateCompany requires company_id field to be set and not empty.');
        }

        $this->sendProducer->updateCompany($companyData);
    }

    /**
     * Updates companies in batch in Moesif.
     */
    public function updateCompaniesBatch(array $companiesBatchData = []) {
        // $this->logger->info("Updating companies in batch in Moesif.", $companiesBatchData);
        foreach ($companiesBatchData as $companyData) {
            if (empty($companyData) || !isset($companyData['company_id'])) {
                // $this->logger->error("Each companyData in updateCompaniesBatch requires a company_id field to be set and not empty.", $companyData);
                throw new Exception('Each companyData in updateCompaniesBatch requires a company_id field to be set and not empty.');
            }
        }

        $this->sendProducer->updateCompaniesBatch($companiesBatchData);
    }

    /**
     * Updates a subscription in Moesif.
     */
    public function updateSubscription(array $subscriptionData) {
        // $this->logger->info("Updating subscription in Moesif.", $subscriptionData);
        if (empty($subscriptionData) || !isset($subscriptionData['subscription_id']) || !isset($subscriptionData['company_id'])) {
            // $this->logger->error("Moesif updateSubscription requires subscription and company id field to be set and not empty.");
            throw new Exception('Moesif updateSubscription requires the subscription ID and company ID fields to be set and not empty.');
        }

        $this->sendProducer->updateSubscription($subscriptionData);
    }

    /**
     * Updates subscriptions in batch in Moesif.
     */
    public function updateSubscriptionsBatch(array $subscriptionsBatchData = []) {
        // $this->logger->info("Updating subscriptions in batch in Moesif.", $subscriptionsBatchData);
        foreach ($subscriptionsBatchData as $subscriptionData) {
            if (empty($subscriptionData) || !isset($subscriptionData['subscription_id']) || !isset($subscriptionData['company_id'])) {
                // $this->logger->error("Each subscriptionData in updateSubscriptionsBatch requires a subscription and company id field to be set and not empty.", $subscriptionData);
                throw new Exception('Each subscriptionData in updateSubscriptionsBatch requires the subscription ID and company ID fields to be set and not empty.');
            }
        }

        $this->sendProducer->updateSubscriptionsBatch($subscriptionsBatchData);
    }

    /**
     * Track an event defined by $event.
     */
    public function track(array $event) {
        // $this->logger->info("Tracking event in Moesif.", $event);
        $this->sendProducer->track($event);
    }

    /**
     * Resets the event queue.
     */
    public function reset() {
        // $this->logger->info("Resetting Moesif events queue.");
        $this->sendProducer->reset();
    }
}
