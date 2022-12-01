<?php

namespace App;

use App\Modules\AsModule;
use App\Modules\ModuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Code\Reflection\ClassReflection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ModulesLoader implements ServiceSubscriberInterface
{
    /**
     * @var array<string>|null
     */
    private static ?array $subscribedServices = null;
    private static ?array $moduleIdMap;

    public function __construct(private readonly ContainerInterface $locator)
    {
    }

    public static function getSubscribedServices(): array
    {
        if (null === self::$subscribedServices) {
            self::$subscribedServices = [];
            self::$moduleIdMap = [];

            $finder = new Finder();
            $finder->files()->in(__DIR__ . '/Modules')->name('*Module.php');
            foreach ($finder as $moduleFile) {
                $subdir = $moduleFile->getRelativePath() ? $moduleFile->getRelativePath() . '\\' : '';
                $class = 'App\\Modules\\' . $subdir . $moduleFile->getFilenameWithoutExtension();
                $module = new ClassReflection($class);
                foreach ($module->getAttributes() as $attribute) {
                    if ($attribute->getName() === AsModule::class) {
                        self::$subscribedServices[] = $module->getName();
                        /** @var AsModule $moduleParameters */
                        $moduleParameters = $attribute->newInstance();
                        self::$moduleIdMap[$moduleParameters->id] = $module->getName();
                    }
                }
            }
        }

        return self::$subscribedServices;
    }

    public function getModuleDataFrom(string $moduleId): array
    {
        $moduleName = self::$moduleIdMap[$moduleId] ?? null;
        if (null === $moduleName) {
            throw new \Exception('Module not found');
        }

        /** @var ModuleInterface $module */
        $module = $this->locator->get($moduleName);
        return $module->getDatas();
    }

    public function sync()
    {
        foreach (self::getSubscribedServices() as $moduleName) {
            /** @var ModuleInterface $module */
            $module = $this->locator->get($moduleName);
            $module->syncDatas();
        }
    }

    private function moduleNameFromId(string $moduleId): string
    {

    }
}