<?php

namespace App\Http\Controllers;

use Socialite;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct() {
        $this->http = new Client(['verify' => false]);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        // Find a better way to manage this when there are mulitple providers
        // 1. Create seperate controllers
        // 2. Take in a provider parameter and call different methods based on that
        return Socialite::driver('github')->stateless()->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $user = Socialite::driver('github')->stateless()->user();


        $response = $this->http->post(route('passport.token'), [
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'social',
                'client_id' => config('services.password_client.client_id'),
                'client_secret' => config('services.password_client.client_secret'),
                'provider' => 'github',
                'access_token' => $user->token,
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $query = $this->getQueryFromResponse($response);

        return Redirect::to(config('client.auth.redirect_url') . '?' . $query);
    }

    private function getQueryFromResponse($response) {
        $authorised = $response->getStatusCode() === Response::HTTP_OK;
        $data = json_decode($response->getBody()->getContents(), true);
        $params = [];

        if ($authorised) {
            $params['status'] = 'success';
            $params['token'] = Arr::get($data, 'access_token');
        } else {
            $params['status'] = 'error';
            $params['message'] = 'Authorisation failed.';
        }

        return http_build_query($params);
    }

}


