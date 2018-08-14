<?php

require 'loader.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username']; // get the value from the username field

$params = [
    'index' => 'places', // the index
    'type' => 'users' // the table or collection
];

$params['body']['query']['match']['username'] = $username;


try {
    $search_response = $client->search($params); // execute the search query

    if($search_response['hits']['total'] == 0){ // if the username doesn't already exist
        // create the user
        $index_response = $client->index([
            'index' => 'places',
            'type' => 'users',
            'id' => $username,
            'body' => [
                'username' => $username
            ]
        ]);
    }

    echo 'ok';

} catch(\Exception $e) {
    echo 'err: ' . $e->getMessage();
}