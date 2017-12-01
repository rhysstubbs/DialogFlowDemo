<?php
/**
 * Created by PhpStorm.
 * User: rhys
 * Date: 01/12/2017
 * Time: 10:53
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class APIController extends Controller
{

    public function index(Request $request)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $body = file_get_contents('php://input');
            $json = json_decode($body, true);

            if (array_get($json, 'result.metadata.intentName') == 'Jobs') {

                $location = strtolower(array_get($json, 'result.parameters.geo-city'));

                $query = DB::table('jobs')
                    ->where('location', $location)
                    ->get();

                $extras = array(
                    'jobCount' => count($query)
                );

                $encodedQuery = json_encode($query);
                if (!empty($encodedQuery)) {

                    $response = $this->buildResponse($encodedQuery, $extras);
                    return response($response, 200);
                }
            }
        }
        return response('', 400);
    }

    public function buildResponse($data, $extras)
    {
        $t = json_decode($data, true);

        foreach($t as $i) {
            $id = $i['id'];
            $job_type = $i['job_type'];
            $location = $i['location'];
        }

        $jobCount = $extras['jobCount'];

        return json_encode(
            array(
                'speech' => $jobCount." ".'jobs found',
                'displayText' => $jobCount." ".$job_type." ". "job in". " ". $location,
                'data' => '',
                'contextOut' => [],
                'source' => 'Demo'
            )
        );
    }

}