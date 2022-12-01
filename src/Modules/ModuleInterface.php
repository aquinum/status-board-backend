<?php

namespace App\Modules;

use App\Entity\ModuleDatas;
use App\Repository\ModuleDatasRepository;
use Doctrine\ORM\EntityManagerInterface;

interface  ModuleInterface
{
    public function getDatas(): array;
    public function syncDatas(): void;
}