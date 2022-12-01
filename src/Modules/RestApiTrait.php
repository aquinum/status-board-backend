<?php

namespace App\Modules;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait RestApiTrait
{
    private string $baseUri;
    private string $apiToken;

    protected function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    protected function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function post(string $path, mixed $data, string $contentType = null): string
    {
        $url = sprintf('%s%s', $this->baseUri, $path);
        return $this->client->request('POST', $url, [
            'headers' => [
                'Content-Type' => $contentType ?? 'application/json',
            ],
            'body' => $data,
        ])->getContent();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function get(string $path): string
    {
        $url = sprintf('%s%s', $this->baseUri, $path);
        return $this->client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->apiToken,
            ],
        ])->getContent();
    }
}