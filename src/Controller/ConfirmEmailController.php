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

use Flarum\Http\SessionAccessToken;
use Flarum\Http\SessionAuthenticator;
use Flarum\Http\UrlGenerator;
use Flarum\User\Command\ConfirmEmail;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

// added by BlockCat
use Flarum\Settings\SettingsRepositoryInterface;
use Maicol07\Flarum\Api\Client as ClientRequest;
use GuzzleHttp\Client as HttpRequest;

class ConfirmEmailController implements RequestHandlerInterface
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var SessionAuthenticator
     */
    protected $authenticator;

    protected $allPortlets = [
        'despre',
        'istoria',
        'cultura',
        'politica',
        'edu',
        'geografia',
        'demografia',
        'economia',
        'cariera',
        'digi',
        'drept',
        'presa',
        'wellness',
        // 'localhost',
    ];

    /**
     * @param Dispatcher $bus
     * @param UrlGenerator $url
     * @param SessionAuthenticator $authenticator
     */
    public function __construct(Dispatcher $bus, UrlGenerator $url, SessionAuthenticator $authenticator)
    {
        $this->bus = $bus;
        $this->url = $url;
        $this->authenticator = $authenticator;
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     */
    public function handle(Request $request): ResponseInterface
    {
        $token = Arr::get($request->getQueryParams(), 'token');

        $user = $this->bus->dispatch(
            new ConfirmEmail($token)
        );

        // activate another accounts
        // added by BlockCat
        $this->activatePortletsAccounts($request, $user->username);

        $session = $request->getAttribute('session');
        $token = SessionAccessToken::generate($user->id);
        $this->authenticator->logIn($session, $token);

        return new RedirectResponse($this->url->to('forum')->base());
    }

    protected function checkEnableExtension($portletName): bool {
        $client = new HttpRequest();
        
        try {
            $response = $client->get("https://{$portletName}.emoldova.org/api/extensions/block-cat-ssup");
        } catch (ClientException $th) {
            return false;
        }

        $response = json_decode($response->getBody());

        return $response->active;
    }

    protected function activatePortletsAccounts(Request $request, $username) {
        // get api token
        $apiToken = resolve(SettingsRepositoryInterface::class)->get('block-cat.api_token');

        foreach ($this->allPortlets as $portlet) {
            // pass registration portlet
            if (str_contains($request->getUri()->getHost(), $portlet)) continue;

            if (!$this->checkEnableExtension($portlet)) continue;

            $client = new ClientRequest("https://{$portlet}.emoldova.org", ['token' => $apiToken]);

            // get user with current $username
            $user = $client->users($username)->request();
            $userId = $user->id;

            // activate email on another portlets
            $client->setPath("/api/users/{$userId}/activate-email");
            $client->post()->request();
        }
    }
}
