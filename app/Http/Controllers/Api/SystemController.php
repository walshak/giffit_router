<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \RouterOS\Client;
use \RouterOS\Query;

class SystemController extends Controller
{
    public function getAddresses()
    {
        // Initiate client with config object
        $client = new Client([
            'timeout' => 1,
            'host'    => 'de3b0d91a42f.sn.mynetname.net',
            'user'    => 'admin',
            'pass'    => '18781875'
        ]);

        // Build query
        $response = $client->query('/ip/address/print')->read();

        // Send query to RouterOS
        // $response = $client->query($query)->read();
        return response()->json($response);
    }
}
