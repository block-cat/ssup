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

use Flarum\User\AccountActivationMailer;
use Flarum\User\Event\Registered;

class CancelActivationMailer extends AccountActivationMailer
{
    public function handle(Registered $event)
    {
        return;
    }
}