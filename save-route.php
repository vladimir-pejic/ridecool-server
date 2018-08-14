<?php

require 'loader.php';

$google_api_key = getenv('GOOGLE_API_KEY');

$data = json_decode(file_get_contents("php://input"), true);
$start_location = $data['start_location']; // an array containing the coordinates (latitude and longitude) of the rider's origin
$end_location = $data['end_location']; // the coordinates of the rider's destination

$username = $data['username']; // the rider's username
$from = $data['from']; // the descriptive name of the rider's origin
$to = $data['to']; // the descriptive name of the rider's destination
$id = generateRandomString(); // unique ID used for identifying the document

$steps_data = [];

$contents = file_get_contents("https://maps.googleapis.com/maps/api/directions/json
                                        ?origin={$start_location['latitude']},{$start_location['longitude']}
                                        &destination={$end_location['latitude']},{$end_location['longitude']}
                                        &key={$google_api_key}");

$directions_data = json_decode($contents, true);


if(!empty($directions_data['routes'])){
    $steps = $directions_data['routes'][0]['legs'][0]['steps'];
    foreach($steps as $step){
        $steps_data[] = [
            'lat' => $step['start_location']['lat'],
            'lng' => $step['start_location']['lng']
        ];

        $steps_data[] = [
            'lat' => $step['end_location']['lat'],
            'lng' => $step['end_location']['lng']
        ];
    }
}

// Construct the data to save in Elasticsearch index
if(!empty($steps_data)){

    $params = [
        'index' => 'places',
        'type' => 'location',
        'id' => $id,
        'body' => [
            'username' => $username,
            'from' => $from,
            'to' => $to,
            'from_coords' => [ // geo-point values needs to have lat and lon
                'lat' => $start_location['latitude'],
                'lon' => $start_location['longitude'],
            ],
            'current_coords' => [
                'lat' => $start_location['latitude'],
                'lon' => $start_location['longitude'],
            ],
            'to_coords' => [
                'lat' => $end_location['latitude'],
                'lon' => $end_location['longitude'],
            ],
            'steps' => $steps_data
        ]
    ];

}


// Request to index data
try{
    $response = $client->index($params);
    $response_data = json_encode([
        'id' => $id
    ]);
    echo $response_data;
}catch(\Exception $e){
    echo 'err: ' . $e->getMessage();
}


// Generate random string for ID
function generateRandomString($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++){
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}