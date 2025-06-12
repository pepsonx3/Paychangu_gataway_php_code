<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/payment_errors.log');

require 'phpmail/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$ref = $_GET['tx_ref'] ?? '';

$secret = "YOUR_SECOND_SECRET_KEY";

$url = "https://api.paychangu.com/verify-payment/$ref";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Authorization: Bearer $secret"
    ]
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

file_put_contents("verify_debug.txt", "URL: $url\nResponse: $response\n\n");

if ($err) die("cURL Error: $err");

$data = json_decode($response, true);
file_put_contents("verify_debug.txt", print_r($data, true), FILE_APPEND);

echo "<pre>" . print_r($data, true) . "</pre>";

if (isset($data['data']['status']) && $data['data']['status'] === 'success') {


    

} else {
    $status = $data['data']['status'] ?? 'N/A';
    $msg = $data['message'] ?? '';
    echo "⏳ Payment not successful yet.";
    echo "<br>Status: $status";
    echo "<br>Message: $msg";
}
?>