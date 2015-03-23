<?php
function deliver_response($format, $api_response){
    // Define HTTP responses
    $http_response_code = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found'
    );
 
    // Set HTTP Response
    header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
 
    // Process different content types
    if ( strcasecmp($format, 'json') == 0 ){
        // Set HTTP Response Content Type
        header('Content-Type: application/json; charset=utf-8');
 
        // Format data into a JSON response
        $json_response = json_encode($api_response);
 
        // Deliver formatted data
        echo $json_response;
    } else {
        // Set HTTP Response Content Type (This is only good at handling string data, not arrays)
        header('Content-Type: text/html; charset=utf-8');
 
        // Deliver formatted data
        echo $api_response['data'];
    }

    // End script process
    exit;
}

// Define whether an HTTPS connection is required
$HTTPS_required = FALSE;
 
// Define API response codes and their related HTTP response
$api_response_code = array(
    0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
    1 => array('HTTP Response' => 200, 'Message' => 'Success'),
    2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
    3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
    4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
    5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
    6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);
 
// Set default HTTP response of 'ok'
$response['code'] = 0;
$response['status'] = 404;
$response['data'] = NULL;
 
// --- Step 2: Authorization
if ( !array_key_exists('component', $_GET) || !array_key_exists('method', $_GET) ) {
    $response['code'] = 5;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = $api_response_code[ $response['code'] ]['Message'];

    deliver_response('json', $response);
}

// Optionally require connections to be made via HTTPS
if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
    $response['code'] = 2;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = $api_response_code[ $response['code'] ]['Message'];
 
    // Return Response to browser. This will exit the script.
    deliver_response($_GET['format'], $response);
}

// --- Step 3: Process Request
require __DIR__ . '/../vendor/autoload.php';
use Common\Config as GlobalConfig;

$ns = $_GET['component'] . '\\REST\\' . $_GET['component'] . 'Service';

if (!class_exists($ns)) {
    $response['code'] = 0;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = $api_response_code[ $response['code'] ]['Message'];

    deliver_response($_GET['format'], $response);
}

GlobalConfig::Instance()->setup( __DIR__.'/../config' );

try {
    $reflectionMethod = new ReflectionMethod($ns, $_GET['method']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $inputArgs = json_decode(file_get_contents('php://input'), true);
        // var_dump($inputArgs);
        $response['code'] = $reflectionMethod->invoke(new $ns, $inputArgs);
    } else {
        $response['code'] = $reflectionMethod->invoke(new $ns);
    }
} catch (Exception $e) {
    $response['code'] = 5;
    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    $response['data'] = $e->getMessage();

    deliver_response($_GET['format'], $response);
}

$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
$response['data'] = $api_response_code[ $response['code'] ]['Message'];

// --- Step 4: Deliver Response
deliver_response($_GET['format'], $response);