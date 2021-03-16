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
    public function insertWhoisRecordsBulk()
    {
        $data = [
            "apiKey" => "at_QIsl2fPxpeDnAvFnSSBkjKdW7YMze",
            "requestId" => "9f1ae70a-b238-443d-a10f-91d28e96caac",
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

                if (isset($decoded[$i]->whoisRecord->createdDate) || isset($decoded[$i]->whoisRecord->registryData->createdDate)
                    && isset($decoded[$i]->whoisRecord->expiresDate) || isset($decoded[$i]->whoisRecord->registryData->expiresDate)
                    && isset($decoded[$i]->whoisRecord->registrarName) || isset($decoded[$i]->whoisRecord->registrant) || isset($decoded[$i]->whoisRecord->registryData->registrant->name) && isset($decoded[$i]->domainName)
                    && isset($decoded[$i]->whoisRecord->registryData->nameServers->rawText)) {
//                if (isset($decoded[$i]->whoisRecord->registryData->createdDate) && isset($decoded[$i]->whoisRecord->registryData->expiresDate) && isset($decoded[$i]->whoisRecord->registryData->registrant->name) && isset($decoded[$i]->domainName)) {
                    if (isset($decoded[$i]->whoisRecord->createdDate)) {
                        $createdDate = $decoded[$i]->whoisRecord->createdDate;
                    } else if (isset($decoded[$i]->whoisRecord->registryData->createdDate)) {
                        $createdDate = $decoded[$i]->whoisRecord->registryData->createdDate;
                    }
                    if (isset($decoded[$i]->whoisRecord->expiresDate)) {
                        $expiresDate = $decoded[$i]->whoisRecord->expiresDate;
                    } else if (isset($decoded[$i]->whoisRecord->registryData->expiresDate)) {
                        $expiresDate = $decoded[$i]->whoisRecord->registryData->expiresDate;
                    }
                    if (isset($decoded[$i]->whoisRecord->registrarName)) {
                        $registrant = $decoded[$i]->whoisRecord->registrarName;
                    } else if (isset($decoded[$i]->whoisRecord->registrant)) {
                        $registrant = $decoded[$i]->whoisRecord->registrant;
                    } else if (isset($decoded[$i]->whoisRecord->registryData->registrant->name)) {
                        $registrant = $decoded[$i]->whoisRecord->registryData->registrant->name;
                    }

                    $domainName = $decoded[$i]->domainName;
                    $hostNames = explode("\n", $decoded[$i]->whoisRecord->registryData->nameServers->rawText);
                    $hostNameTrimmed = trim(substr($hostNames[0], strpos($hostNames[0], '.') + 1));

                    $whois = new Whois([
                        'createdDate' => $createdDate,
                        'expiresDate' => $expiresDate,
                        'registrant' => $registrant,
                        'domainName' => $domainName,
                        'hostName' => $hostNameTrimmed
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
            $record->hostName = $updatedWhois->hostName;

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
                $record->hostName = $updatedWhois->hostName;

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
                if (isset($value->registryData->nameServers->rawText)) {
                    $hostNames = explode("\n", $value->registryData->nameServers->rawText);
                    $hostNameTrimmed = trim(substr($hostNames[0], strpos($hostNames[0], '.') + 1));

                    $whois->hostName = $hostNameTrimmed;
                } else {
                    $whois->hostName = 'Ikke oplyst';
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

