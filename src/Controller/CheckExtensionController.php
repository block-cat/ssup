<?php

/*
 * This file is part of block-cat/ssup.
 *
 * Copyright (c) 2022 block-cat.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace BlockCat\SSUP\Controller;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Flarum\Extension\ExtensionManager;
use Laminas\Diactoros\Response\JsonResponse;

class CheckExtensionController implements RequestHandlerInterface
{
    /**
     * @var ExtensionManager
     */
    protected $extensions;

    public function __construct(ExtensionManager $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $extensionName = Arr::get($request->getQueryParams(), 'name');

        $enabled = $this->extensions->isEnabled($extensionName);

        return new JsonResponse([
            'active' => $enabled
        ]);
    }
}
