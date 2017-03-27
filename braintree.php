<?php 

require __DIR__ . '/vendor/autoload.php';
$access_token = json_decode(file_get_contents('./config.json'))->access_token;
$gateway = new Braintree_Gateway(array(
    'accessToken' => $access_token,
));

$data = json_decode(file_get_contents('php://input'), true);

$result = $gateway->transaction()->sale([
    "amount" => $data['amount'],
    'merchantAccountId' => 'USD',
    "paymentMethodNonce" => $data['nonce']
]);

if ($result->success) {
    http_response_code(200);
    $response = json_encode([
        "transaction_id" => $result->transaction->id
    ]);
    echo $response;
} else {
    http_response_code(400);
    $response = json_encode([
        "error" => $result->message
    ]);
    echo $response;
}
