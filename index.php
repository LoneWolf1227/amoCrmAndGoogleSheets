<?php

require_once __DIR__ . '/amoCrmClientForApiV4/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/fromAmoToSheets.php';
require_once __DIR__ . '/functions/fromSheetsToAmo.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
//log_data($data);

$toSheets = fromAmoToSheets();
$toAmo = fromSheetsToAmo($data);

//Функция подключения к Google Sheets Api
function serviceClient(){
    $client = new \Google_Client();
    $client->setApplicationName('Amocrm and Sheets');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    return new Google_Service_Sheets($client);
}

function log_data($data) {
    $file = __DIR__.'/log.txt';
    file_put_contents($file, var_export($data, true), FILE_APPEND);
}
