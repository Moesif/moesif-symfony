<?php

namespace Moesif\MoesifBundle\Service;

use Exception;
use Moesif\MoesifBundle\Producer\SendTaskProducer;

class MoesifApiService {

    private $sendProducer;

    /**
     * Instantiates a new MoesifApi instance.
     * The SendTaskProducer dependency is injected via the service container.
     */
    public function __construct(SendTaskProducer $sendProducer) {
        $this->sendProducer = $sendProducer;
    }

    /**
     * Add an array representing a message to be sent to Moesif to the in-memory queue.
     */
    public function enqueue(array $message = []) {
        $this->sendProducer->enqueue($message);
    }

    /**
     * Add an array representing a list of messages to be sent to Moesif to a queue.
     */
    public function enqueueAll(array $messages = []) {
        $this->sendProducer->enqueueAll($messages);
    }

    /**
     * Flush the events queue.
     */
    public function flush(int $desiredBatchSize = 10) {
        $this->sendProducer->flush($desiredBatchSize);
    }

    /**
     * Updates a user in Moesif.
     */
    public function updateUser(array $userData) {
        if (empty($userData) || !isset($userData['user_id'])) {
            throw new Exception('Moesif updateUser requires user_id field to be set and not empty.');
        }

        $this->sendProducer->updateUser($userData);
    }

    /**
     * Updates users in batch in Moesif.
     */
    public function updateUsersBatch(array $usersBatchData = []) {
        foreach ($usersBatchData as $userData) {
            if (empty($userData) || !isset($userData['user_id'])) {
                throw new Exception('Each userData in updateUsersBatch requires a user_id field to be set and not empty.');
            }
        }

        $this->sendProducer->updateUsersBatch($usersBatchData);
    }

    /**
     * Updates a company in Moesif.
     */
    public function updateCompany(array $companyData) {
        if (empty($companyData) || !isset($companyData['company_id'])) {
            throw new Exception('Moesif updateCompany requires company_id field to be set and not empty.');
        }

        $this->sendProducer->updateCompany($companyData);
    }

    /**
     * Updates companies in batch in Moesif.
     */
    public function updateCompaniesBatch(array $companiesBatchData = []) {
        foreach ($companiesBatchData as $companyData) {
            if (empty($companyData) || !isset($companyData['company_id'])) {
                throw new Exception('Each companyData in updateCompaniesBatch requires a company_id field to be set and not empty.');
            }
        }

        $this->sendProducer->updateCompaniesBatch($companiesBatchData);
    }

    /**
     * Track an event defined by $event.
     */
    public function track(array $event) {
        $this->sendProducer->track($event);
    }

    /**
     * Resets the event queue.
     */
    public function reset() {
        $this->sendProducer->reset();
    }
}
