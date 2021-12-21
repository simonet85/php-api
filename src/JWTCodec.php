<?php 
class JWTCodec{
    private $key;
    public function __construct( $key ){
        $this->key = $key;
    }

    public function encode(array $payload): string{
        //Header
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);

        $header = $this->base64urlEncode( $header );

        //Payload
        $payload = json_encode($payload);
        $payload = $this->base64urlEncode( $payload );

        //Signature
        //Generate a keyed hash value using the HMAC method
        $signature = hash_hmac("sha256", $header.".".$payload,$this->key, true);

        $signature = $this->base64urlEncode($signature);

        return $header .".". $payload .".". $signature;
    }

    //Custom decode() decode function 
    public function decode( string $token ): array{

       if( preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
        $token,
        $matches) !== 1){
            throw new InvalidArgumentException("Invalid token format");
        }

        $signature = hash_hmac("sha256", $matches["header"].".".$matches["payload"],$this->key, true);

        $signature_from_token = $this->base64urlDecode( $matches["signature"]);

        if( ! hash_equals( $signature, $signature_from_token )){
            throw new InvalidSignatureException;
        }

        $payload = json_decode( $this->base64urlDecode($matches["payload"]), true);

        return $payload;
    }

    //Custom base64urlEncode() encode function , PHP doesn't have base64urlEncode function
    private function base64urlEncode( string $text ): string{

        return str_replace(
            ["+", "/", "="], //search : +, / ,=
            ["-", "_", ""], // replace with : -, _, ""
            base64_encode( $text ) //within : text
        );
    }

    private function base64urlDecode( string $text ): string{

        return base64_decode(
            str_replace(
                ["-", "_"], 
                ["+", "/"],
                $text 
            )
        );
    }
}