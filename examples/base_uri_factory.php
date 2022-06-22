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

use CoiSA\Http\Message\BaseUriFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$baseUriFactory = new BaseUriFactory('https://httpbin.org/');

var_dump(
    (string) $baseUriFactory->createUri('/get?test=123'),
    (string) $baseUriFactory->createUri('get'),
    (string) $baseUriFactory->createUri('/status/404'),
    (string) $baseUriFactory->createUri('status/404'),
);
