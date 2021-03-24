<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class FileController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    function uploadFiles(Request $request)
    {
        $searchTermsArr = [];
        $searchKeywordArr = [];

        // Hvis filen search_term_report ikke er null så kaldes funktionen filterSearchTerms.
        // Resultatet af funktionskaldet sættes til searchTermsArr
        if ($request->file('search_terms_report') !== null) {
            $searchTermsArr = $this->filterSearchTerms($request->file('search_terms_report'));
        }

        // Hvis filen search_keyword_report ikke er null så kaldes funktionen filterSearchKeyword.
        // Resultatet af funktionskaldet sættes til $searchKeywordArr
        if ($request->file('search_keyword_report') !== null) {
            $searchKeywordArr = $this->filterSearchKeyword($request->file('search_keyword_report'));
        }

        // Tjekker op på hvilke elementer der ikke er i begge kollektioner
        // og opretter et nt array med disse elementer
        $tempArr = array_udiff($searchTermsArr, $searchKeywordArr, 'strcasecmp');

        $arrToReturn = [];

        // For hvert item der er i tempArr oprettes der et search objekt.
        // Objektet indsættes i arrToReturn
        foreach ($tempArr as $item) {
            $search = new Search([
                'search' => $item
            ]);
            array_push($arrToReturn, $search);
        }

        // Returnerer arrToReturn
        return $arrToReturn;
    }

    /**
     * @param $file
     * @return array
     */
    function filterSearchTerms($file)
    {
        // Opretter et nyt Xlsx reader objekt
        $searchTermsReader = new Xlsx();

        // Indlæser filen og konverterer aktive regnearksdokument til et array
        $searchTermsArr = $searchTermsReader->load($file)->getActiveSheet()->toArray(false, false, false, true);;

        // Array variabel
        $filteredSearchTermsArr = [];

        // Lopper igennem searchTermsArr og henter alle værdier fra kolonnen A
        // og indsætter værdierne i filteredSearchTermsArr
        foreach ($searchTermsArr as $val) {
            array_push($filteredSearchTermsArr, $val['A']);
        }

        // Kalder array_slice på kollektionen for at fjerne de første 3 elementer
        $slicedSearchTermsArr = array_slice($filteredSearchTermsArr, 3);

        // Kalder array_splice for at fjerne de sidste 8 elementer
        array_splice($slicedSearchTermsArr, count($slicedSearchTermsArr) - 8);

        // Returnerer kollektionen
        return $slicedSearchTermsArr;
    }

    /**
     * @param $file
     * @return array
     */
    function filterSearchKeyword($file)
    {
        // Opretter et nyt Xlsx reader objekt
        $searchKeyword = new Xlsx();

        // Indlæser filen og konverterer aktive regnearksdokument til et array
        $searchKeywordArr = $searchKeyword->load($file)->getActiveSheet()->toArray(false, false, false, true);;

        // Array variabel
        $filteredSearchKeyword = [];

        // Lopper igennem $searchKeywordArr og henter alle værdier fra kolonnen B
        // og indsætter værdierne i filteredSearchTermsArr
        foreach ($searchKeywordArr as $val) {
            array_push($filteredSearchKeyword, $val['B']);
        }

        // Kalder array_slice på kollektionen for at fjerne de første 3 elementer
        $slicedSearchKeyword = array_slice($filteredSearchKeyword, 3);

        // Kalder array_splice for at fjerne de sidste 7 elementer
        array_splice($slicedSearchKeyword, count($slicedSearchKeyword) - 7);

        // Array variabel
        $tempArr = [];

        // Looper igennem slicedSearchKeyword for at fjerne tegn/symboler, spacing foran string
        // og indsætter den nye værdi i tempArr
        foreach ($slicedSearchKeyword as $val) {
            // Fjerner tegn/symboler
            $removeSymb = str_replace(str_split('\\/[]"+'), '', $val);

            // Fjerner spacing foran en string
            $removeSpaces = ltrim($removeSymb);

            // Indsætter den nye værdi i tempArr
            array_push($tempArr, $removeSpaces);
        }

        // Returnerer tempArr
        return $tempArr;
    }
}

