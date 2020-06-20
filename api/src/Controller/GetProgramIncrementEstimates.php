<?php

namespace App\Controller;

use App\Entity\ProgramIncrement;
use App\Handler\GettingEstimatesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetProgramIncrementEstimates extends AbstractController
{
    public function __invoke(ProgramIncrement $data, GettingEstimatesHandler $handler): array
    {
        return $handler($data);
    }
}
