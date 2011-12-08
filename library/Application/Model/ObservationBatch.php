<?php

namespace Application\Model;

/**
 * Resource for a observation batch upload operation.
 */
class ObservationBatch 
{
    const STATUS_COMPLETE = 'complete';
    const STATUS_PENDING = 'pending';
    
    private $id;
    private $status;
    private $author;
    private $startedAt;
    private $completedAt;
    private $progress;
    private $submittedData;
    private $pendingData;
    private $failedData;
    private $errors;
    
    public function processNextPending() {
        if (count($this->pendingData) == 0) {
            $this->status = self::STATUS_COMPLETE;
            return;
        }
        
        $nextPendingData = $pendingData[0];
    }
}
