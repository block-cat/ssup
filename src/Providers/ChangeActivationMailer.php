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
use Flarum\User\AccountActivationMailer;

class ChangeActivationMailer extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->bind(AccountActivationMailer::class, CancelActivationMailer::class);
    }
}