<?php 
class JWTCodec{
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
        $signature = hash_hmac("sha256", $header.".".$payload,"3778214125442A472D4B6150645367566B59703273357638792F423F4528482B", true);

        $signature = $this->base64urlEncode($signature);

        return $header .".". $payload .".". $signature;
    }

    //Custom base64urlEncode() function , PHP doesn't have base64urlEncode function
    private function base64urlEncode( string $text ): string{

        return str_replace(
            ["+", "/", "="], //search : +, / ,=
            ["-", "_", ""], // replace with : -, _, ""
            base64_encode( $text ) //within : text
        );
    }
}