<?php 
$payload = [
    "sub"  => $user["id"],
    "name" => $user["name"],
    "exp"  => time() + 20 //20 seconds
];

$jwt = $codec->encode( $payload );

//Issue refresh_token
$refresh_token_expiry = time() + 432000; // 5 days
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    // "sub" => 0,//Invalid user ID
    "exp" => $refresh_token_expiry
]);


echo json_encode([
    // "access_token" => $access_token
    "jwt" => $jwt,
    "refresh_token" => $refresh_token
]);

// echo $access_token;