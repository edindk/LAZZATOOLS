<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
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
        // Opretter et nyt GuzzleHttp Client objekt
        $http = new Client;

        // Response fra HTTP requestet
        $response = null;

        try {
            // Udsteder access_token
            $response = $http->post('http://api.lazzatools.dk/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => 2,
                    'client_secret' => 'EvER7o5ADOP5wAiy1ozuvmaQRabv2Lh9tiHlg6VL',
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);

            // Afkoder reponse for at få fat i access_token
            $response = json_decode($response->getBody()->getContents());

            // Henter brugeroplysninger ud fra access_token
            $user = $this->retrieveUser($response);

            // Hvis brugerens email er verificeret returner access_token
            if ($user->email_verified_at) {
                return $response->access_token;
            }

            // Hvis registerToken er true returner access_token
            if ($registerToken) {
                return $response;
            }

            // Ellers så kaldes /logout endpointet for at fjerne alle generede access_tokens
            else {
                $http->post('https://api.lazzatools.dk/api/logout', [
                    'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $response->access_token]
                ]);

                // Thrower en ny exception
                throw new Exception('Email not verified', 500);
            }
        }
        // Alt efter hvilken exception der opstår, returneres der en passende fejlmeddelelse
        catch (BadResponseException $e) {
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

        // Funktionen sendVerification tager et $request i parameteren
        // og returnerer en HTTP response
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
        $http = new Client;
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
        // Opretter nyt Client objekt
        $http = new Client;

        // Henter brugeroplysninger
        $userResponse = $http->get('https://api.lazzatools.dk/api/user', [
            'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $response->access_token]
        ]);

        // Returnerer afkodede brugeroplysninger
        return json_decode($userResponse->getBody()->getContents());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Fjerner alle access_tokens fra databasen der er tilknyttet brugeren
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        // Returnerer json besked
        return response()->json('Logged out successfully', 200);
    }
}

