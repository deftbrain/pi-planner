<?php

namespace App\Controller;

use App\Entity\ProgramIncrement;
use App\Handler\GettingEstimatesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetProgramIncrementEstimates extends AbstractController
{
    /**
     * @param ProgramIncrement $data Warning: the parameter MUST be called $data!
     * @see https://api-platform.com/docs/core/controllers/#creating-custom-operations-and-controllers
     */
    public function __invoke(ProgramIncrement $data, GettingEstimatesHandler $handler): array
    {
        return $handler($data);
    }
}
