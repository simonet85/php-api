<?php 
$payload = [
    "sub"  => $user["id"],
    "name" => $user["name"],
    "exp"  => time() + 20 //20 seconds
];

$jwt = $codec->encode( $payload );

//Issue refresh_token
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    // "sub" => 0,//Invalid user ID
    "exp" => time() + 432000 // 5 days
]);


echo json_encode([
    // "access_token" => $access_token
    "JWT" => $jwt,
    "refresh_token" => $refresh_token
]);

// echo $access_token;