<?php

namespace App\Modules;

use App\Entity\ModuleDatas;
use App\Repository\ModuleDatasRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractModule implements ModuleInterface
{
    protected readonly ModuleDatasRepository $moduleDatasRepository;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
        /** @var ModuleDatasRepository $moduleDatasRepository */
        $moduleDatasRepository = $this->entityManager->getRepository(ModuleDatas::class);
        $this->moduleDatasRepository = $moduleDatasRepository;
    }

    public function getDatas(): array
    {
        return [
            'module' => $this->getModuleParameters()->id,
            'datas' => array_map(function (ModuleDatas $moduleData) {
                return [
                    'timestamp' => $moduleData->getUpdatedAt()->format('U'),
                    'data' => $moduleData->getDatas(),
                ];
            }, $this->getModuleDatas())
        ];
    }

    /**
     * @return array<int, ModuleDatas>
     */
    protected function getModuleDatas(): array
    {
        $moduleParameters = $this->getModuleParameters();
        return $this->moduleDatasRepository->findByModule($moduleParameters->id);
    }

    protected function getModuleParameters(): AsModule
    {
        $reflection = new \ReflectionClass($this);
        $attributes = $reflection->getAttributes(AsModule::class);
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === AsModule::class) {
                /** @var AsModule $attributeObject */
                return $attribute->newInstance();
            }
        }
        throw new \LogicException();
    }
}