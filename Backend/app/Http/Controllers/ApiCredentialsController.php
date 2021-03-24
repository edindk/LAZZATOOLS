<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiCredentialsController extends Controller
{
    /**
     * Indsætter api-nøgle til WhoisXmlApi i databasen
     *
     * @param Request $request
     */
    public function insertWhoisXmlApiCredentials(Request $request)
    {
        $result = DB::table('api_credentials')->where('name', 'WhoisXmlApi')->get();
        $result->toArray();

        if (!count($result)) {
            $values = array('name' => 'WhoisXmlApi', 'key' => $request->key, 'username' => 'null', 'status' => 'active');
            DB::table('api_credentials')->insert($values);
        } else {
            DB::table('api_credentials')->where('name', 'WhoisXmlApi')->update(['name' => 'WhoisXmlApi', 'key' => $request->key, 'username' => 'null', 'status' => 'active']);
        }
    }
}
