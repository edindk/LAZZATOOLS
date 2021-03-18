<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;


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

        $tempArr = array_udiff($searchTermsArr, $searchKeywordArr, 'strcasecmp');
        $arrToReturn = [];


        foreach ($tempArr as $item) {
            $search = new Search([
                'search' => $item
            ]);
            array_push($arrToReturn, $search);
        }

        return $arrToReturn;
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

