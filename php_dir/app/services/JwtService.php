<?php

class JwtService {
    private $secret;

    public function __construct($secret = null) {
        $this->secret = $secret ?: getenv('JWT_SECRET') ?: 'change-me-in-production';
    }

    public function encode(array $payload) {
        $header = array('alg' => 'HS256', 'typ' => 'JWT');
        $headerSegment = $this->base64UrlEncode(json_encode($header));
        $payloadSegment = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $headerSegment . '.' . $payloadSegment, $this->secret, true);
        $signatureSegment = $this->base64UrlEncode($signature);

        return $headerSegment . '.' . $payloadSegment . '.' . $signatureSegment;
    }

    public function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        list($headerSegment, $payloadSegment, $signatureSegment) = $parts;
        $expectedSignature = $this->base64UrlEncode(hash_hmac('sha256', $headerSegment . '.' . $payloadSegment, $this->secret, true));

        if (!hash_equals($expectedSignature, $signatureSegment)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadSegment), true);
        if (!is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && time() > $payload['exp']) {
            return null;
        }

        return $payload;
    }

    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

?>
