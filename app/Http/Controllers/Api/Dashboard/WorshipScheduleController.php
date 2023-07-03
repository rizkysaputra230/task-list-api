<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Http;

class WorshipScheduleController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Http([
            'base_uri' => "https://api.myquran.com/v1/",
        ]);
    }

    public function index()
    {
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        try {
            $res = $this->client->request('GET', "sholat/jadwal/1301/{$year}/{$month}/{$day}");
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $decoded = json_decode($res->getBody()->getContents());

        return response()->json($decoded->data->jadwal);
    }
}
