<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RecommendationEventsService
{
    private $pythonScriptPath;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->pythonScriptPath = $parameterBag->get('kernel.project_dir') . '/public/PythonScript/EventRecommendation.py';
    }

    public function recommendEvents(array $userProfiles, User $user)
    {
        
        //Serialize user profiles data to pass it to Python script
        $user_data=[
            'userId' => strval($user->getId()),
            'userProfiles'=>$userProfiles,
        ];
        $userProfilesSerialized = json_encode($user_data);
        
        $id= strval($user->getId());
        
        
        // Execute Python script
        $process = new Process(['python', $this->pythonScriptPath, $userProfilesSerialized ]);
        $process->run(); 

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
            echo "process is not successful";
        }

        // Get the recommended events from the output
        $recommendedEventsJson = $process->getOutput(); //why they are null
        //echo "json".$recommendedEventsJson."end";
        $recommendedEvents = json_decode($recommendedEventsJson, true);
        
        
        if ($recommendedEvents === null) {
            // Log the error
            error_log('Error decoding recommended events JSON: ' . json_last_error_msg());
            echo json_last_error_msg() ;
            // Return an empty array or provide a default response
            return [];
        }

        // Check if the expected key exists in the decoded JSON
        if (!isset($recommendedEvents['recommendations'])) {
            // Log the error
            error_log('Expected key "recommendations" not found in decoded JSON.');

            // Return an empty array or provide a default response
            return [];
        }

        // Return recommended events
        return $recommendedEvents['recommendations'];
    }
}



/* */