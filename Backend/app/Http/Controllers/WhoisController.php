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
    // Henter data på alle domæner
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

    // Opdaterer alle whois records
    public function updateAllWhoisRecords()
    {
        $listOfWhoisRecords = Whois::all();
        $arr = [];

        foreach ($listOfWhoisRecords as $record) {
            $updatedWhois = $this->getWhoisRecordFromApi($record->domainName);
            $record->createdDate = $updatedWhois->createdDate;
            $record->expiresDate = $updatedWhois->expiresDate;
            $record->registrant = $updatedWhois->registrant;
            $record->domainName = $updatedWhois->domainName;

            array_push($arr, $record);
            $record->save();
        }
        return $arr;
    }

    // Opdaterer en enkel whois record
    public function updateWhoisRecord(Request $request)
    {
        $listOfWhoisRecords = Whois::all();

        foreach ($listOfWhoisRecords as $record) {
            if ($record->domainName == $request->domainName) {
                $updatedWhois = $this->getWhoisRecordFromApi($record->domainName);
                $record->createdDate = $updatedWhois->createdDate;
                $record->expiresDate = $updatedWhois->expiresDate;
                $record->registrant = $updatedWhois->registrant;
                $record->domainName = $updatedWhois->domainName;

                $record->save();
                return $record;
            }
        }
    }

    // Henter whois data ved kald til whoisxmlapi
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

    // Gemmer whois record i DB
    public function storeWhois(Request $request)
    {
        $whois = $this->getWhoisRecordFromApi($request->domain);
        $whois->save();
    }

    // Returner alle whois records fra DB
    public function getAllWhoisRecords()
    {
        $listOfWhoisRecords = Whois::all();

        return $listOfWhoisRecords;
    }

    // Henter statuskode ved brug af GuzzleHttp
    public function getStatusCode(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        try {
            $status = $http->get($request->domainName)->getStatusCode();
        } catch (ConnectException | ServerException | RequestException $e) {
            $status = 500;
        }
        return $status;
    }


    // Sletter domænet fra DB
    public function deleteDomain(Request $request)
    {
        DB::table('whois')->where('domainName', $request->domain)->delete();
    }
}
