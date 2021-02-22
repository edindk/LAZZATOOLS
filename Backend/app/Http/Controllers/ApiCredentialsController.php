<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiCredentialsController extends Controller
{
    public function getWhoisXmlApiCredentials()
    {
        $result = DB::select('SELECT * FROM api_credentials');
    }

    public function insertWhoisXmlApiCredentials(Request $request)
    {
        // Sætter alle andre nøgler til inaktive
        $updateValues = array('status' => 'inactive');
        DB::table('api_credentials')->update($updateValues);

        // Indsætter den nye nøgle
        $values = array('name' => 'WhoisXmlApi', 'key' => $request->key, 'username' => 'null', 'status' => 'active');
        DB::table('api_credentials')->insert($values);
    }
}
