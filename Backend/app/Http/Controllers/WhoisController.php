<?php

namespace App\Http\Controllers;

use App\Whois;
use DateTime;
use Illuminate\Http\Request;

class WhoisController extends Controller
{
    public function checkList($listOfWhoisRecords)
    {
        $arr = [];
        $currentDate = new DateTime('now');

        foreach ($listOfWhoisRecords as $record) {
            $givenDate = new DateTime($record->expiresDate);

            if ($givenDate > $currentDate) {
                array_push($arr, $record);
            } else {
                $updatedWhois = $this->getWhoisRecordFromApi($record->domainName);

                $record->createdDate = $updatedWhois->createdDate;
                $record->expiresDate = $updatedWhois->expiresDate;
                $record->registrant = $updatedWhois->registrant;
                $record->domainName = $updatedWhois->domainName;

                array_push($arr, $record);
                $record->save();
            }
        }
        return $arr;
    }

    public function getWhoisRecordFromApi($domain)
    {
        $apiKey = 'at_PiWTiCVTAJocXPd0uUi8imriFCJs4';
        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->get('https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=' . $apiKey . '&domainName=' . $domain . '&outputFormat=JSON');
            foreach (json_decode($response->getBody()) as $response) {
                $whois = new Whois([
                    'createdDate' => $response->registryData->createdDate,
                    'expiresDate' => $response->registryData->expiresDate,
                    'registrant' => $response->registryData->registrant->name,
                    'domainName' => $response->domainName,
                ]);
                return $whois;
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request.');
            }
            return response()->json('Something went wrong on the server', $e->getCode());
        }
    }

    public function storeWhois(Request $request)
    {
        $whois = $this->getWhoisRecordFromApi($request->domain);
        $whois->save();
    }

    public function getAllWhoisRecords()
    {
        $listOfWhoisRecords = Whois::all();
        $updatedList = $this->checkList($listOfWhoisRecords);

        return $updatedList;
    }
}
