<?php

/*
 * This file is part of block-cat/ssup.
 *
 * Copyright (c) 2022 block-cat.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace BlockCat\SSUP\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Http\RouteCollection;
use Illuminate\Contracts\Container\Container;
use Flarum\Http\RouteHandlerFactory;
use BlockCat\SSUP\Controller\ConfirmationEmailController;

class ChangeRegisterController extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->resolving('flarum.forum.routes', function (RouteCollection $routes, Container $container) {
            $this->setRegisterController($routes, $container);
        });
    }

    protected function setRegisterController(RouteCollection $routes, Container $container) {
        $routes->removeRoute("confirmEmail.submit");

        $factory = $container->make(RouteHandlerFactory::class);

        $routes->post(
            '/confirm/{token}',
            'confirmEmail.submit',
            $factory->toController(ConfirmationEmailController::class)
        );
    }
}