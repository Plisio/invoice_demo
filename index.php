<?php
$DIR = dirname(__FILE__);
require_once implode(DIRECTORY_SEPARATOR, [$DIR, 'vendor', 'autoload.php']);

$config = include('settings.php');
try {
    $orderObject = new PlisioOrderRepository($config['dsn'], $config['db_username'], $config['db_password']);
} catch (PDOException $e) {
    die($e->getMessage());
}

$plisioSecretKey = $config['secret_key'];
$client = new \Plisio\ClientAPI($plisioSecretKey);

if (isset($_GET['page'])) {
    // handle invoice updates (Plisio's callback)
    if ($_GET['page'] === 'callback' && isset($_POST) && !empty($_POST)) {
        if ($client->verifyCallbackData($_POST, $plisioSecretKey)) {
            $data = $_POST;
            $data['order_id'] = $data['order_number'];
            $data['plisio_invoice_id'] = $data['txn_id'];

            if ($orderObject->updateOrder($data)) {
                header("HTTP/1.1 200 OK");
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
        }
    // display white-label invoice page and handle ajax updates
    } elseif ($_GET['page'] === 'invoice' && isset($_GET['invoice_id']) && !empty($_GET['invoice_id'])) {
        $order = $orderObject->get($_GET['invoice_id']);
        $order['expire_utc'] = (new DateTime($order['expire_utc']))->getTimestamp() * 1000;
        if (!empty($order['tx_urls'])) {
            try {
                $txUrl = json_decode($order['tx_urls']);
                if (!empty($txUrl)) {
                    $txUrl = gettype($txUrl) === 'string' ? $txUrl : $txUrl[count($txUrl) - 1];
                    $order['txUrl'] = $txUrl;
                }
            } catch (Exception $e) {
                //TODO: log error $e
                return;
            }
        }
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($order);
            die();
        } else {
            include_once(implode(DIRECTORY_SEPARATOR, [$DIR, 'pages', 'header.php']));
            include_once(implode(DIRECTORY_SEPARATOR, [$DIR, 'pages', 'invoice.php']));
            include_once(implode(DIRECTORY_SEPARATOR, [$DIR, 'pages', 'footer.php']));
        }
    }
} else {

    $validators = [
        'amount' => FILTER_VALIDATE_FLOAT,
        'order_number' => FILTER_VALIDATE_INT,
        'amount_usd' => FILTER_VALIDATE_FLOAT,
    ];

    //validate user input
    $errors = [];
    foreach ($validators as $field => $validator) {
        if (isset($_POST[$field])) {
            if (!filter_var($field, $validator)) {
                $errors[$field] = 'Invalid ' . $field . ' value';
            }
        }
    }
    if (empty($errors)) {

        //prepare invoice data
        $data = array(
            'order_number' => $_POST['order_number'],
            'order_name' => $_POST['order_name'],
            'description' => $_POST['description'],
            'currency' => $_POST['currency'],
            'callback_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/?page=callback',
//        'email' => $_POST['email']
        );

        if (isset($_POST['amount_usd']) && $_POST['amount_usd'] == true) {
            $data['source_amount'] = $_POST['amount_usd'];
            $data['source_currency'] = 'USD';
        } else {
            $data['amount'] = number_format($_POST['amount'], 8, '.', '');
        }

        $response = $client->createTransaction($data);

        if ($response && $response['status'] !== 'error' && !empty($response['data'])) {
            $whiteLabel = isset($response['data']['wallet_hash']);
            if ($orderObject->add(array_merge($response['data'], [
                'order_id' => $_POST['order_number'],
                'plisio_invoice_id' => $response['data']['txn_id']
            ]), $whiteLabel)) {
                if ($whiteLabel){
                    header('Location: ?page=invoice&invoice_id=' . $response['data']['txn_id']);
                } else {
                    header('Location: ' . $response['data']['invoice_url']);
                }
            } else {
                die('');
            }
        } else {
            $errors = json_decode($response['data']['message'], true);
        }
    }

    $currencies = $client->getCurrencies();
    if (isset($currencies['status']) && $currencies['status'] === 'success' && isset($currencies['data'])) {
        $currencies = $currencies['data'];
        include(implode(DIRECTORY_SEPARATOR, [$DIR, 'pages', 'form.php']));
    } else {
        throw new Exception('Plisio server is not accessible');
    }
}


if (isset($orderObject)) {
    $orderObject->close();
}
