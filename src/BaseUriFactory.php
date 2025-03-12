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
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class BaseUriFactory.
 *
 * @package CoiSA\Http\Message
 */
class BaseUriFactory implements UriFactoryInterface
{
    private UriInterface $baseUri;

    /**
     * BaseUriFactory constructor.
     *
     * @param string|UriInterface $baseUri
     */
    public function __construct($baseUri, ?UriFactoryInterface $uriFactory = null)
    {
        if (!$baseUri instanceof UriInterface) {
            $uriFactory ??= Psr17FactoryDiscovery::findUriFactory();
            $baseUri = $uriFactory->createUri((string) $baseUri);
        }

        $this->baseUri = $baseUri;
    }

    public function getBaseUri(): UriInterface
    {
        return $this->baseUri;
    }

    /**
     * @throws \InvalidArgumentException When the given URI appears to be malformed
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $uriParts = parse_url($uri);

        if (false === $uriParts) {
            throw new \InvalidArgumentException('The source URI string appears to be malformed');
        }

        $path = sprintf('%s/%s', $this->baseUri->getPath(), ltrim($uriParts['path'], '/'));

        return $this->baseUri->withPath($path)
            ->withQuery($uriParts['query'] ?? '')
            ->withFragment($uriParts['fragment'] ?? '')
        ;
    }
}
