<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class AuthController extends Controller
{

    /**
     * @param Request $request
     * @param null $registerToken
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(Request $request, $registerToken = null)
    {
        $http = new \GuzzleHttp\Client;
        $response = null;
        try {
            $response = $http->post('http://api.lazzatools.dk/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => 2,
                    'client_secret' => 'EvER7o5ADOP5wAiy1ozuvmaQRabv2Lh9tiHlg6VL',
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);
            $response = json_decode($response->getBody()->getContents());

            $user = $this->retrieveUser($response);

            if ($user->email_verified_at) {
                return $response->access_token;
            }
            if ($registerToken) {
                return $response;
            } else {
                $logoutResponse = $http->post('https://api.lazzatools.dk/api/logout', [
                    'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $response->access_token]
                ]);

                throw new Exception('Email not verified', 500);
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            }
            if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        } catch (Exception $e) {
            if ($e->getMessage() == 'Email not verified') {
                return response()->json('Email not verified', $e->getCode());
            }
        }
    }

    /**
     * @param Request $request
     * @return \Psr\Http\Message\StreamInterface
     */
    public function register(Request $request)
    {
        // Validering af name, email og password
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|regex:/lazzaweb.dk/',
            'password' => 'required|string|min:6',
        ]);

        // Oprettelse af user objektet
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $response = $this->sendVerification($request);
        return $response->getBody();
    }

    /**
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendVerification(Request $request)
    {
        // Funktionskald til login for at få access_token
        $loginResponse = $this->login(new Request(['username' => $request->email, 'password' => $request->password]), $registerToken = true);

        // Sender verificerings mail
        $http = new \GuzzleHttp\Client;
        return $http->get('https://api.lazzatools.dk/api/email/resend', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $loginResponse->access_token
            ]
        ]);
    }

    /**
     * @param $response
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function retrieveUser($response)
    {
        $http = new \GuzzleHttp\Client;

        $userResponse = $http->get('https://api.lazzatools.dk/api/user', [
            'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $response->access_token]
        ]);

        return json_decode($userResponse->getBody()->getContents());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }
}

