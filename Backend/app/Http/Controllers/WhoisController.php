<?php

namespace App\Http\Controllers;

use App\Whois;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WhoisController extends Controller
{
    public function getWhosisRecord(Request $request)
    {
        $domain = $request->domain;
        $apiKey = 'at_PiWTiCVTAJocXPd0uUi8imriFCJs4';
        $http = new \GuzzleHttp\Client;
        $whoisDB = null;

        try {
            $whoisDB = Whois::where('domainName', '=', $domain)->firstOrFail();
        } catch (Exception $e) {
            $whoisDB = null;
        }


        if ($whoisDB) {
            return $whoisDB;
        } else {

            try {
                $response = $http->get('https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=' . $apiKey . '&domainName=' . $domain . '&outputFormat=JSON');
                foreach (json_decode($response->getBody()) as $response) {
                    $whois = new Whois([
                        'createdDate' => $response->audit->createdDate,
                        'updatedDate' => $response->audit->updatedDate,
                        'expiresDate' => $response->registryData->expiresDate,
                        'registrant' => $response->registryData->registrant->name,
                        'domainName' => $response->domainName,
                    ]);
                    $whois->save();
                    return $whois;
                }
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                if ($e->getCode() === 400) {
                    return response()->json('Invalid Request.');
                }
                return response()->json('Something went wrong on the server', $e->getCode());
            }
        }
    }

    public function getAllWhoisRecords()
    {
        return Whois::all();
    }
}
