<?php 

require __DIR__ . '/vendor/autoload.php';
$gateway = new Braintree_Gateway(array(
  'accessToken' => 'access_token$sandbox$5q6d7x6kqhs8wnm8$925d1c4326325bbf8b64a710680c2d01',
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
