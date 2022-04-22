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

use Flarum\Api\Client;
use Flarum\Http\RememberAccessToken;
use Flarum\Http\Rememberer;
use Flarum\Http\SessionAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

use Maicol07\Flarum\Api\Client as ClientRequest;
use Maicol07\SSO\Flarum;
use Illuminate\Support\Arr;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
// use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Flarum\User\AccountActivationMailerTrait;
use Flarum\Http\UrlGenerator;
use Illuminate\Contracts\Queue\Queue;

class RegisterController implements RequestHandlerInterface
{
    use AccountActivationMailerTrait;

    /**
     * @var Client
     */
    protected $api;

    /**
     * @var SessionAuthenticator
     */
    protected $authenticator;

    /**
     * @var Rememberer
     */
    protected $rememberer;

    protected $translator;
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param Client $api
     * @param SessionAuthenticator $authenticator
     * @param Rememberer $rememberer
     */
    public function __construct(Client $api,
        SessionAuthenticator $authenticator,
        Rememberer $rememberer, 
        Translator $translator,
        SettingsRepositoryInterface $settings,
        Queue $queue,
        UrlGenerator $url)
    {
        $this->api = $api;
        $this->authenticator = $authenticator;
        $this->rememberer = $rememberer;
        $this->translator = $translator;
        $this->settings = $settings;
        $this->queue = $queue;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): ResponseInterface
    {
        $params = ['data' => ['attributes' => $request->getParsedBody()]];

        // list of all portlets
        $allPortlets = [
            // 'despre',
            // 'istoria',
            // 'cultura',
            // 'politica',
            // 'edu',
            // 'geografia',
            // 'demografia',
            // 'economia',
            // 'cariera',
            // 'digi',
            // 'drept',
            // 'presa',
            'localhost',
            'wellness',
        ];

        // data sent from user
        $username = Arr::get($request->getParsedBody(), 'username');
        $email = Arr::get($request->getParsedBody(), 'email');
        $password = Arr::get($request->getParsedBody(), 'password');

        // get api token
        $apiToken = resolve(SettingsRepositoryInterface::class)->get('block-cat.api_token');

        // $client = new ClientRequest("http://localhost/flarum", ['token' => $apiToken]);
        // // $client->addQueryParameter('id', 23);
        // $client->setPath('/flarum/api/users/23/activate-email');
        // $client->post()->request();
        // return new EmptyResponse;

        // verify if user already exists on another portlets
        foreach ($allPortlets as $portlet) {
            // verify localhost portlet
            if (str_contains($request->getUri()->getHost(), 'localhost')) {
                $client = new ClientRequest("http://localhost/flarum", ['token' => $apiToken]);
            } else {
                $client = new ClientRequest("https://{$portlet}.emoldova.org", ['token' => $apiToken]);
            }

            // verify if email adress already used
            $allUsers = $client->users()->request();
            $allUsers = $allUsers->collect()->toArray();
            
            foreach ($allUsers as $user) {
                if (strcmp($user->attributes['email'], $email) == 0) {
                    return new TextResponse('Aceasta adresa de email deja este utilizata!', 412);
                }
            }

            try {
                // if user aren't exist it will generate ClientException
                $data = $client->users($username)->request();
                
                // if user already exists $data will contained user data and will be return TextResponse
                // throw new ValidationException(['file' => $this->translator->trans('core.api.invalid_username_message')]);
                return new TextResponse('Exista deja un utilizator cu asa un username!', 412);
            } catch (ClientException $th) {
                if ($th->getCode() !== 404) {
                    throw $th;
                }
            }
        }

        // register user on all portlets
        foreach ($allPortlets as $portlet) {
            // pass current portlet
            if (str_contains($request->getUri()->getHost(), $portlet)) continue;

            $forum = new Flarum([
                'url' => "https://{$portlet}.emoldova.org",
                'api_key' => $apiToken
            ]);

            $newUser = $forum->user($username);
            $newUser->attributes->email = $email;
            $newUser->attributes->password = $password;

            try {
                $registred = $newUser->signup();
            } catch (ServerException $th) {}
        }

        // original from Flarum
        $response = $this->api->withParentRequest($request)->withBody($params)->post('/users');

        $body = json_decode($response->getBody());
        
        if (isset($body->data)) {
            $userId = $body->data->id;

            // send email to confirm accounts
            // added by BlockCat
            $this->sendEmailConfirmation($userId);

            $token = RememberAccessToken::generate($userId);

            $session = $request->getAttribute('session');
            $this->authenticator->logIn($session, $token);

            $response = $this->rememberer->remember($response, $token);
        }

        return $response;
    }

    protected function sendEmailConfirmation($userId) {
        $user = User::query()->find($userId);

        if ($user->is_email_confirmed) {
            return;
        }

        $token = $this->generateToken($user, $user->email);
        $data = $this->getEmailData($user, $token);
        
        $this->sendConfirmationEmail($user, $data);
    }
}
