<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class Dbscan
{
    public function performClustering(array $data): string
    {
        $pythonScriptPath = 'C:\xampp\htdocs\BellyBump\scripts\python_scripts\dbscan_script.py';
        $dataJson = json_encode($data);
        $process = new Process(['python', $pythonScriptPath, $dataJson]);
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    
        return $process->getOutput();
    }
    

}