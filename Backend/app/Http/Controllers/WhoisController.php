<?php

namespace App\Http\Controllers;

use App\Whois;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class WhoisController extends Controller
{

    /**
     * Henter data på alle domæner
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function insertWhoisRecordsBulk()
    {
        // Array der indeholder nødvendig information for at hente data på domænerne
        $data = [
            "apiKey" => "at_QIsl2fPxpeDnAvFnSSBkjKdW7YMze",
            "requestId" => "9f1ae70a-b238-443d-a10f-91d28e96caac",
            "maxRecords" => 100,
            "startIndex" => 1,
            "decode_content" => true,
            "outputFormat" => "JSON"
        ];

        // Opretter et nyt GuzzleHttp Client objekt
        $http = new Client;


        try {
            // Henter data
            $response = $http->post('https://www.whoisxmlapi.com/BulkWhoisLookup/bulkServices/getRecords', [
                'body' => json_encode($data),
                'headers' => ['Accept' => 'application/json']
            ]);

            // Afkoder datasættet
            $decoded = json_decode(utf8_encode($response->getBody()->getContents()));
//            var_dump(json_last_error());
//            var_dump(json_last_error_msg());

            // Får fat i alle records
            $decoded = $decoded->whoisRecords;

            //Lopper igennem kollektionen og tjekker op på om de forskellige værdier er sat
            for ($i = 0; $i < count($decoded); $i++) {

                if (isset($decoded[$i]->whoisRecord->createdDate) || isset($decoded[$i]->whoisRecord->registryData->createdDate)
                    && isset($decoded[$i]->whoisRecord->expiresDate) || isset($decoded[$i]->whoisRecord->registryData->expiresDate)
                    && isset($decoded[$i]->whoisRecord->registrarName) || isset($decoded[$i]->whoisRecord->registrant) || isset($decoded[$i]->whoisRecord->registryData->registrant->name) && isset($decoded[$i]->domainName)
                    && isset($decoded[$i]->whoisRecord->registryData->nameServers->rawText)) {

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

                    // Deler stringen ved linjeskiftet
                    $hostNames = explode("\n", $decoded[$i]->whoisRecord->registryData->nameServers->rawText);

                    // Fjerner første del af stringen
                    $hostNameTrimmed = trim(substr($hostNames[0], strpos($hostNames[0], '.') + 1));

                    // Opretter et nyt Whois objekt og sætter alle dens props til værdierne fra WhoisXMLAPI
                    $whois = new Whois([
                        'createdDate' => $createdDate,
                        'expiresDate' => $expiresDate,
                        'registrant' => $registrant,
                        'domainName' => $domainName,
                        'hostName' => $hostNameTrimmed
                    ]);

                    // Indsætter objektet i databasen
                    $whois->save();
                } // Hvis ingen af værdierne er sat sætes objektet til null
                else {
                    $whois = null;
                }
            }
        } catch (Exception $e) {

        }
    }

    /**
     * Opdaterer alle whois records
     *
     * @return array
     */
    public function updateAllWhoisRecords()
    {
        // Henter alle Whois data fra databasen
        $listOfWhoisRecords = Whois::all();

        // Array variabel
        $arr = [];

        // Looper igennem listOfWhoisRecords. For hvert element hentes der nyeste data ud fra domænenavnet.
        // Opdaterer med ny data.
        foreach ($listOfWhoisRecords as $record) {
            $updatedWhois = $this->getWhoisRecordFromApi($record->domainName);
            $record->createdDate = $updatedWhois->createdDate;
            $record->expiresDate = $updatedWhois->expiresDate;
            $record->registrant = $updatedWhois->registrant;
            $record->domainName = $updatedWhois->domainName;
            $record->hostName = $updatedWhois->hostName;

            // Indsætter opdaterede element i arr
            array_push($arr, $record);

            // Indsætter opdaterede element i databasen
            $record->save();
        }

        // Returnerer arr
        return $arr;
    }


    /**
     * Opdaterer en enkel whois record
     *
     * @param Request $request
     * @return Whois|mixed
     */
    public function updateWhoisRecord(Request $request)
    {
        // Henter alle whois data fra databasen
        $listOfWhoisRecords = Whois::all();

        // Looper igennem kollektionen og tjekker op på
        // om domænet er det samme der er sendt med i requestet
        // Derefter opdateres alle værdierne
        foreach ($listOfWhoisRecords as $record) {
            if ($record->domainName == $request->domainName) {
                $updatedWhois = $this->getWhoisRecordFromApi($record->domainName);
                $record->createdDate = $updatedWhois->createdDate;
                $record->expiresDate = $updatedWhois->expiresDate;
                $record->registrant = $updatedWhois->registrant;
                $record->domainName = $updatedWhois->domainName;
                $record->hostName = $updatedWhois->hostName;

                // Det opdaterede element indsættes i databasen
                $record->save();

                // Returnerer det opdaterede element
                return $record;
            }
        }
    }

    /**
     * Henter whois data ved kald til WhoisXMLAPI
     *
     * @param $domain
     * @return Whois|\Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWhoisRecordFromApi($domain)
    {
        // Henter api-nøgle fra databasen
        $result = DB::table('api_credentials')->where('status', 'active')->first();

        // Variabel der indeholder api-nøglen
        $apiKey = $result->key;

        // Opretter et nyt GuzzleHttp Client objekt
        $http = new Client;

        try {
            // Henter domænedata fra WhoisXMLAPI
            $response = $http->get('https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=' . $apiKey . '&domainName=' . $domain . '&outputFormat=JSON');

            // Afkoder HTTP response
            $response = json_decode($response->getBody()->getContents());

            // Looper igennem for hvert element i response kollektionen
            foreach ($response as $value) {

                // Opretter nyt Whois objekt
                $whois = new Whois();

                // Tjekker op på om værdierne er sat
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

            // Returnerer objektet
            return $whois;

            // Fejlhåndtering
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request.');
            }
            return response()->json('Something went wrong on the server', $e->getCode());
        }
    }

    /**
     * Indsætter whois record i databasen
     *
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function storeWhois(Request $request)
    {
        // Henter domænedata ud fra domæne
        $whois = $this->getWhoisRecordFromApi($request->domain);

        // Indsætter whois i databasen
        $whois->save();
    }

    /**
     * Returnerer alle whois records fra databasen
     *
     * @return Whois[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllWhoisRecords()
    {
        // Henter alle records fra databasen
        $listOfWhoisRecords = Whois::all();

        // Returnerer kollektionen
        return $listOfWhoisRecords;
    }

    /**
     * Henter statuskode ved brug af GuzzleHttp
     *
     * @param Request $request
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatusCode(Request $request)
    {
        //Opretter et nyt GuzzleHttp Client objekt
        $http = new Client;

        try {
            // Laver et HTTP request og får fat i status koden
            $status = $http->get($request->domainName)->getStatusCode();
        } catch (ConnectException | ServerException | RequestException $e) {
            $status = 500;
        }

        // Returnerer status koden
        return $status;
    }

    /**
     * Sletter domænet fra databasen
     *
     * @param Request $request
     */
    public function deleteDomain(Request $request)
    {
        // Sletter domænedata ud fra medsendt domæne i requestet
        DB::table('whois')->where('domainName', $request->domain)->delete();
    }
}

