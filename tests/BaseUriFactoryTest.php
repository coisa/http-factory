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
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 * @coversDefaultClass \CoiSA\Http\Message\BaseUriFactory
 */
final class BaseUriFactoryTest extends TestCase
{
    use ProphecyTrait;

    private string $baseUrl;

    private ObjectProphecy $uriFactory;

    private ObjectProphecy $uri;

    private BaseUriFactory $baseUriFactory;

    protected function setUp(): void
    {
        $this->baseUrl    = uniqid('baseUrl');
        $this->uriFactory = $this->prophesize(UriFactoryInterface::class);
        $this->uri        = $this->prophesize(UriInterface::class);

        $this->uri->__toString()->willReturn($this->baseUrl);
        $this->uriFactory->createUri(Argument::type('string'))->willReturn($this->uri->reveal());

        $this->baseUriFactory = new BaseUriFactory(
            $this->baseUrl,
            $this->uriFactory->reveal()
        );
    }

    /**
     * @coversNothing
     */
    public function testClassImplementsPsrUriFactoryInterface(): void
    {
        parent::assertInstanceOf(UriFactoryInterface::class, $this->baseUriFactory);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorCanBeInvokedWithoutUriFactory(): void
    {
        $factory = new BaseUriFactory(uniqid('baseUrl'));

        parent::assertInstanceOf(UriInterface::class, $factory->getBaseUri());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorAllowPsrUriOnBaseUrlArgument(): void
    {
        $uri     = $this->uri->reveal();
        $factory = new BaseUriFactory($uri);

        parent::assertSame($uri, $factory->getBaseUri());
    }

    /**
     * @covers ::getBaseUri
     */
    public function testGetBaseUriWillReturnUriWithGivenBaseUrl(): void
    {
        parent::assertSame($this->baseUrl, (string) $this->baseUriFactory->getBaseUri());
    }

    /**
     * @covers ::createUri
     */
    public function testCreateUriWithMalformedUriWillThrowInvalidArgumentException(): void
    {
        parent::expectException(\InvalidArgumentException::class);

        $this->baseUriFactory->createUri(':');
    }

    /**
     * @covers ::createUri
     */
    public function testCreateUriWillMergeBaseUrlWithGivenUri(): void
    {
        $query    = http_build_query([uniqid('key') => uniqid('value')]);
        $fragment = uniqid('fragment');
        $path     = sprintf('%s/%s/%s', uniqid('path'), uniqid('path'), uniqid('path'));
        $uri      = sprintf('%s?%s#%s', $path, $query, $fragment);

        $this->uri->getPath()->willReturn($this->baseUrl)->shouldBeCalledOnce();
        $this->uri->withPath($this->baseUrl . '/' . $path)->willReturn($this->uri->reveal())->shouldBeCalledOnce();
        $this->uri->withQuery($query)->willReturn($this->uri->reveal())->shouldBeCalledOnce();
        $this->uri->withFragment($fragment)->willReturn($this->uri->reveal())->shouldBeCalledOnce();

        parent::assertSame($this->uri->reveal(), $this->baseUriFactory->createUri($uri));
    }
}
