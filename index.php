<?php
$DIR = dirname(__FILE__);
require_once implode(DIRECTORY_SEPARATOR, [$DIR, 'vendor', 'autoload.php']);

$config = include('settings.php');
try {
    $dbh = new PDO($config['dsn']);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
initDb($dbh);

$client = new \Plisio\ClientAPI(ini_get($config['secret_key']));

if (!isset($_GET['page'])) {
    $currencies = $client->getCurrencies();
    if (isset($currencies['status']) && $currencies['status'] === 'success' && isset($currencies['data'])) {
        $currencies = $currencies['data'];
        include(implode(DIRECTORY_SEPARATOR, [$DIR, 'pages', 'form.php']));
    } else {
        throw new Exception('Plisio server is not accessible');
    }
} else {

}


function initDb($dbh){
    try {
        $query = file_get_contents('plisio.sql');
        $dbh->query($query);
    } catch (Exception $e){
        die($e->getMessage());
    }
}