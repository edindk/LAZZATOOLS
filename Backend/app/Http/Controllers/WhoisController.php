<?php

namespace App\Http\Controllers;

use App\Whois;
use DateTime;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use function MongoDB\BSON\toJSON;

class WhoisController extends Controller
{
    public function getWhoisRecordsBulk()
    {
        $data = [
            "apiKey" => "at_PiWTiCVTAJocXPd0uUi8imriFCJs4",
            "requestId" => "59c3d40e-946f-44f3-a0be-d6f6c42dfd93",
            "maxRecords" => 100,
            "startIndex" => 1,
            "decode_content" => true,
            "outputFormat" => "JSON"
        ];

        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->post('https://www.whoisxmlapi.com/BulkWhoisLookup/bulkServices/getRecords', [
                'body' => json_encode($data),
                'headers' => ['Accept' => 'application/json']
            ]);

            $decoded = json_decode(utf8_encode($response->getBody()->getContents()));
//            var_dump(json_last_error());
//            var_dump(json_last_error_msg());

            $decoded = $decoded->whoisRecords;

            for ($i = 0; $i < count($decoded); $i++) {

                if (isset($decoded[$i]->whoisRecord->registryData->createdDate) && isset($decoded[$i]->whoisRecord->registryData->expiresDate) && isset($decoded[$i]->whoisRecord->registryData->registrant->name) && isset($decoded[$i]->domainName)) {
                    $createdDate = $decoded[$i]->whoisRecord->registryData->createdDate;
                    $expiresDate = $decoded[$i]->whoisRecord->registryData->expiresDate;
                    $registrant = $decoded[$i]->whoisRecord->registryData->registrant->name;
                    $domainName = $decoded[$i]->domainName;

                    $whois = new Whois([
                        'external_id' => null,
                        'createdDate' => $createdDate,
                        'expiresDate' => $expiresDate,
                        'registrant' => $registrant,
                        'domainName' => $domainName
                    ]);
                    $whois->save();
                } else {
                    $whois = null;
                }
            }
        } catch (Exception $e) {

        }
    }

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
            $response = json_decode($response->getBody()->getContents());
            foreach ($response as $value) {

                $whois = new Whois();
                if (isset($value->registryData->createdDate)) {
                    $whois->createdDate = $value->registryData->createdDate;
                } else {
                    $whois->createdDate = 'Ikke oplyst';
                }
                if (isset($value->registryData->expiresDate)) {
                    $whois->expiresDate = $value->registryData->expiresDate;
                } else {
                    $whois->expiresDate = 'Ikke oplyst';
                }
                if (isset($value->registryData->registrant->name)) {
                    $whois->registrant = $value->registryData->registrant->name;
                } else if (isset($value->registrant->name)) {
                    $whois->registrant = $value->registrant->name;
                } else {
                    $whois->registrant = 'Ikke oplyst';
                }
                if (isset($value->domainName)) {
                    $whois->domainName = $value->domainName;
                } else {
                    $whois->registrant = 'Ikke oplyst';
                }
            }
            return $whois;
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
            } catch (ConnectException | ServerException | RequestException $e) {
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
