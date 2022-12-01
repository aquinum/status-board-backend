<?php

namespace App\Controller;

use App\ModulesLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ApiController
{
    #[Route('/api/{module}', methods: ['GET'])]
    public function serve(string $module, ModulesLoader $loader): JsonResponse
    {
        $data = $loader->getModuleDataFrom($module);

        return new JsonResponse($data);
    }
}