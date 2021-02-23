<?php

namespace App\Http\Controllers;

use App\Whois;
use DateTime;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use function MongoDB\BSON\toJSON;

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
        return $this->getStatusCode($arr);
    }

    public function getWhoisRecordFromApi($domain)
    {
        $result = DB::table('api_credentials')->where('status', 'active')->first();

        $apiKey = $result->key;
        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->get('https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=' . $apiKey . '&domainName=' . $domain . '&outputFormat=JSON');
            foreach (json_decode($response->getBody()) as $response) {
                $whois = new Whois([
                    'external_id' => '',
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

    public function getStatusCode($arr)
    {
        $http = new \GuzzleHttp\Client;
        foreach ($arr as $element) {
            try {
                $element->status = $http->get($element->domainName)->getStatusCode();
            } catch (ConnectException $e) {
                $element->status = 500;
            }
        }
        return $arr;
    }

    // Sletter domænet fra DB
    public function deleteDomain(Request $request)
    {
        DB::table('whois')->where('domainName', $request->domain)->delete();
    }
}
