<?php

namespace App\Modules\Netatmo;

use App\Entity\ApiTokens;
use App\Modules\RestApiTrait;
use App\Repository\ApiTokensRepository;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use LogicException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NetatmoApi
{
    use RestApiTrait;

    const BASE_URI = 'https://api.netatmo.com';
    const SCOPE = 'read_homecoach';

    public function __construct(
        private readonly ApiTokensRepository $apiTokensRepository,
        private readonly HttpClientInterface $client,
        private readonly string              $clientId,
        private readonly string              $clientSecret,
        private readonly string              $username,
        private readonly string              $password,
    )
    {
        $this->setBaseUri(self::BASE_URI);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchData(): string
    {
        $this->login();

        return $this->get('/api/gethomecoachsdata');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchTokens(): array
    {
        $contentType = 'application/x-www-form-urlencoded;charset=UTF-8';
        $body = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password,
            'scope' => self::SCOPE,
        ];
        $content = $this->post('/oauth2/token', $body, $contentType);

        return json_decode($content, true);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function refreshTokens(string $refreshToken): array
    {
        $contentType = 'application/x-www-form-urlencoded;charset=UTF-8';
        $body = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];
        $content = $this->post('/oauth2/token', $body, $contentType);

        return json_decode($content, true);
    }

    /**
     * @throws Exception|TransportExceptionInterface
     */
    private function login(): void
    {
        $apiTokens = $this->apiTokensRepository->findOneByServiceName('netatmo');
        $netatmoTokens = null;
        if (!$apiTokens) {
            $netatmoTokens = $this->fetchTokens();
            $apiTokens = new ApiTokens();
        }
        if ($apiTokens && $apiTokens->getExpiresAt() < new DateTimeImmutable()) {
            $netatmoTokens = $this->refreshTokens($apiTokens->getRefreshToken());
        }
        if ($netatmoTokens) {
            $apiTokens
                ->setServiceName('netatmo')
                ->setAccessToken($netatmoTokens['access_token'])
                ->setExpiresAt(new DateTimeImmutable(
                    sprintf('+%s seconds', $netatmoTokens['expires_in']),
                    new DateTimeZone('UTC')
                ))
                ->setRefreshToken($netatmoTokens['refresh_token']);
            $this->apiTokensRepository->save($apiTokens, true);
        }
        if (!$apiTokens) {
            throw new LogicException();
        }
        $this->setApiToken(sprintf('Bearer %s', $apiTokens->getAccessToken()));
    }
}