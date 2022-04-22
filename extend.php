<?php

/*
 * This file is part of block-cat/ssup.
 *
 * Copyright (c) 2022 block-cat.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace BlockCat\SSUP;

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),
    new Extend\Locales(__DIR__ . '/resources/locale'),
    (new Extend\Settings())
        -> serializeToForum('block-cat.apiToken', 'block-cat.api_token'),
    (new Extend\ServiceProvider())
        // ->register(Providers\ChangeRegisterController::class)
        ->register(Providers\ChangeActivationMailer::class),
    (new Extend\Routes("api"))
        ->post(
            '/users/{id}/activate-email',
            'users.email.activate',
            Controller\ActivateEmailController::class
        ),
    (new Extend\Routes("forum"))
        ->remove('register')
        ->post(
            '/register',
            'register',
            Controller\RegisterController::class
        )
        ->remove('confirmEmail.submit')
        ->post(
            '/confirm/{token}',
            'confirmEmail.submit',
            Controller\ConfirmEmailController::class
        ),
];
