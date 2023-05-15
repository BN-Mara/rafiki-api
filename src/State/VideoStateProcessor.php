<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\NotificationService;

class VideoStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private NotificationService $notifyer){

    }
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Handle the state
       
    
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->notifyAll();
        return $result;
    }
    private function notifyAll(){
        $this->notifyer->notifyAll("New Video Added", "New Video has been added","VIDEO_UPLOADED");
    }
}
