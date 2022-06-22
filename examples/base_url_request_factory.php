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

use CoiSA\Http\Message\BaseUrlRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$requestFactory = new BaseUrlRequestFactory('https://httpbin.org/');

var_dump(
    (string) $requestFactory->createRequest('GET', '/get?test=123')->getUri(),
    (string) $requestFactory->createRequest('GET', 'get')->getUri(),
    (string) $requestFactory->createRequest('GET', '/status/404')->getUri(),
    (string) $requestFactory->createRequest('GET', 'status/404')->getUri(),
);
