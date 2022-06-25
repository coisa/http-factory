<?php

declare(strict_types=1);

/**
 * This file is part of coisa/http-factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/http-factory
 * @copyright Copyright (c) 2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Http\Message;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class BaseUrlRequestFactory.
 *
 * @package CoiSA\Http\Message
 */
final class BaseUrlRequestFactory implements RequestFactoryInterface
{
    private UriFactoryInterface $uriFactory;

    private RequestFactoryInterface $requestFactory;

    /**
     * @param BaseUriFactory|string|UriInterface $baseUrl
     */
    public function __construct(
        $baseUrl,
        RequestFactoryInterface $requestFactory = null,
        UriFactoryInterface $uriFactory = null
    ) {
        if (!$baseUrl instanceof BaseUriFactory) {
            $baseUrl = new BaseUriFactory($baseUrl, $uriFactory);
        }

        $this->uriFactory     = $baseUrl;
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->requestFactory->createRequest(
            $method,
            $this->uriFactory->createUri($uri)
        );
    }
}
