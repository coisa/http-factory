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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 * @coversDefaultClass \CoiSA\Http\Message\BaseUrlRequestFactory
 */
final class BaseUrlRequestFactoryTest extends TestCase
{
    use ProphecyTrait;

    private string $baseUrl;

    private ObjectProphecy $requestFactory;

    private ObjectProphecy $request;

    private ObjectProphecy $uriFactory;

    private ObjectProphecy $uri;

    private BaseUrlRequestFactory $baseUrlRequestFactory;

    protected function setUp(): void
    {
        $this->baseUrl = uniqid('baseUrl');

        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->request        = $this->prophesize(RequestInterface::class);
        $this->uriFactory     = $this->prophesize(UriFactoryInterface::class);
        $this->uri            = $this->prophesize(UriInterface::class);

        $this->requestFactory->createRequest(
            Argument::type('string'),
            Argument::type('string')
        )->willReturn($this->request->reveal());

        $this->uriFactory->createUri(Argument::type('string'))->willReturn($this->uri->reveal());

        $this->uri->getPath()->willReturn($this->baseUrl);
        $this->uri->withPath(Argument::any())->willReturn($this->uri->reveal());
        $this->uri->withQuery(Argument::any())->willReturn($this->uri->reveal());
        $this->uri->withFragment(Argument::any())->willReturn($this->uri->reveal());

        $this->baseUrlRequestFactory = new BaseUrlRequestFactory(
            $this->baseUrl,
            $this->requestFactory->reveal(),
            $this->uriFactory->reveal()
        );
    }

    /**
     * @coversNothing
     */
    public function testClassImplementsPsrRequestFactoryInterface(): void
    {
        parent::assertInstanceOf(RequestFactoryInterface::class, $this->baseUrlRequestFactory);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructWithBaseUriFactoryWillUseGivenBaseUriFactoryToCreateRequest(): void
    {
        $method = uniqid('method');
        $uri    = uniqid('uri');

        $baseUriFactory = $this->prophesize(BaseUriFactory::class);
        $baseUriFactory->createUri($uri)->willReturn($this->uri->reveal())->shouldBeCalledOnce();

        $this->requestFactory->createRequest($method, $this->uri->reveal())->willReturn($this->request->reveal());

        $this->baseUrlRequestFactory = new BaseUrlRequestFactory(
            $baseUriFactory->reveal(),
            $this->requestFactory->reveal(),
            $this->uriFactory->reveal()
        );

        $this->baseUrlRequestFactory->createRequest($method, $uri);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorCanBeInvokedWithoutUriFactory(): void
    {
        $factory = new BaseUrlRequestFactory(uniqid('baseUrl'), $this->requestFactory->reveal());

        parent::assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorCanBeInvokedWithoutRequestFactory(): void
    {
        $factory = new BaseUrlRequestFactory(uniqid('baseUrl'), null, $this->uriFactory->reveal());

        parent::assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    /**
     * @covers ::getBaseUriFactory
     */
    public function testGetBaseUriFactoryWillReturnGivenBaseUriFactory(): void
    {
        $baseUriFactory = $this->prophesize(BaseUriFactory::class);

        $this->baseUrlRequestFactory = new BaseUrlRequestFactory(
            $baseUriFactory->reveal(),
            $this->requestFactory->reveal(),
            $this->uriFactory->reveal()
        );

        parent::assertSame($baseUriFactory->reveal(), $this->baseUrlRequestFactory->getBaseUriFactory());
    }

    /**
     * @covers ::createRequest
     */
    public function testCreateRequestWillProxyArgumentsToRequestFactoryPrefixingUriWithBaseUrl(): void
    {
        $method   = uniqid('method');
        $uri      = uniqid('uri');

        $this->requestFactory->createRequest($method, $this->uri->reveal())->shouldBeCalledOnce();

        $this->baseUrlRequestFactory->createRequest($method, $uri);
    }
}
