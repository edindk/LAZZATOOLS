<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;

class FileController extends Controller
{
    function uploadFiles(Request $request)
    {
        $searchTermsArr = [];
        $searchKeywordArr = [];

        if ($request->file('search_terms_report') !== null) {
            $searchTermsArr = $this->filterSearchTerms($request->file('search_terms_report'));
        }
        if ($request->file('search_keyword_report') !== null) {
            $searchKeywordArr = $this->filterSearchKeyword($request->file('search_keyword_report'));
        }

        $tempArr = [];

        $tempArr = array_udiff($searchTermsArr, $searchKeywordArr, 'strcasecmp');


//        for ($iSearchTerms = 0; $iSearchTerms < count($searchTermsArr); $iSearchTerms++) {
//            for ($iSearchKeyword = 0; $iSearchKeyword < count($searchKeyword); $iSearchKeyword++) {
//                if ($searchTermsArr[$iSearchTerms] == $searchKeyword[$iSearchKeyword]) {
////                    var_dump($searchTermsArr[$iSearchTerms]);
////                    $searchTermsArr[$iSearchTerms] = '';
//
//                    $searchWord = new Search(
//                        [
//                            'searchWord' => $searchTermsArr[$iSearchTerms],
//                            'status' => 'EXISTS'
//                        ]
//                    );
//
//                    array_push($tempArr, $searchWord);
//                    unset($searchTermsArr[$iSearchTerms]);
//                } else {
//                    $searchWord = new Search([
//                        'searchWord' => $searchTermsArr[$iSearchTerms],
//                        'status' => 'DOES NOT EXIST'
//                    ]);
//                    array_push($tempArr, $searchWord);
//                    unset($searchTermsArr[$iSearchTerms]);
//                }
//            }
//        }
        return $tempArr;
    }

    function filterSearchTerms($file)
    {
        // Search Terms report
        $searchTermsReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $searchTermsArr = $searchTermsReader->load($file)->getActiveSheet()->toArray(false, false, false, true);;

        $filteredSearchTermsArr = [];

        foreach ($searchTermsArr as $val) {
            array_push($filteredSearchTermsArr, $val['A']);
        }
        $slicedSearchTermsArr = array_slice($filteredSearchTermsArr, 3);
        array_splice($slicedSearchTermsArr, count($slicedSearchTermsArr) - 8);

        return $slicedSearchTermsArr;
    }

    function filterSearchKeyword($file)
    {
        // Search Keyword report
        $searchKeyword = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $searchKeywordArr = $searchKeyword->load($file)->getActiveSheet()->toArray(false, false, false, true);;

        $filteredSearchKeyword = [];

        foreach ($searchKeywordArr as $val) {
            array_push($filteredSearchKeyword, $val['B']);
        }

        $slicedSearchKeyword = array_slice($filteredSearchKeyword, 3);
        array_splice($slicedSearchKeyword, count($slicedSearchKeyword) - 7);

        $tempArr = [];

        foreach ($slicedSearchKeyword as $val) {
            $removeSymb = str_replace(str_split('\\/[]"+'), '', $val);
            $removeSpaces = ltrim($removeSymb);
            array_push($tempArr, $removeSpaces);
        }
        return $tempArr;
    }
}

