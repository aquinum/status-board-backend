<?php

namespace App\Modules\Netatmo;

use App\Entity\ModuleDatas;
use App\Modules\AbstractModule;
use App\Modules\AsModule;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[AsModule('netatmo', expirationInterval: 11)]
class NetatmoModule extends AbstractModule
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly NetatmoApi $api,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($entityManager);
    }

    public function syncDatas(): void
    {
        $utcTimezone = new \DateTimeZone('UTC');
        $now = new \DateTimeImmutable('now', $utcTimezone);
        $expirationIntervalInSeconds = $this->getModuleParameters()->expirationInterval * 60;

        $moduleDatas = $this->getModuleDatas();
        /** @var ModuleDatas $lastModuleData */
        $lastModuleDataExpiresAt = null;
        foreach ($moduleDatas as $moduleData) {
            $interval = $moduleData->getUpdatedAt()->diff($now);
            $intervalInSeconds = (new \DateTime())->setTimeStamp(0)->add($interval)->getTimeStamp();
            $intervalInMinutes = floor($intervalInSeconds / 60);
            if ($intervalInMinutes > $this->getModuleParameters()->expirationInterval) {
                $this->logger->info('Module data expired. Removing.');
                $this->moduleDatasRepository->remove($moduleData);
            }
            $lastModuleDataExpiresAt = max($moduleData->getExpiresAt(), $lastModuleDataExpiresAt);
        }

        if ($lastModuleDataExpiresAt > new \DateTimeImmutable()) {
            $this->logger->info('Data is fresh. Not fetching.');
            return;
        }

        $datas = json_decode($this->api->fetchData(), true);
        $device = $datas['body']['devices'][0];
        $dashboardData = $device['dashboard_data'];
        $relevantData = [
            'health' => $dashboardData['health_idx'],
            'temp' => $dashboardData['Temperature'],
            'co2' => $dashboardData['CO2'],
            'humidity' => $dashboardData['Humidity'],
            'noise' => $dashboardData['Noise'],
        ];
        $lastUpdatedAt = new \DateTimeImmutable('@' . $device['last_status_store']);
        $expiresAt = $lastUpdatedAt->add(new \DateInterval(sprintf('PT%sS', $expirationIntervalInSeconds)));
        $newData = new ModuleDatas($this->getModuleParameters()->id, $expiresAt);
        $newData
            ->setDatas($relevantData)
            ->setUpdatedAt($lastUpdatedAt);

        $this->entityManager->persist($newData);
        $this->entityManager->flush();
    }
}