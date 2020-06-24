<?php
header("Status: Created", true, "201");
log_data(headers_sent());

require __DIR__ . '/amoCrmClientForApiV4/autoload.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Core.php';


//Данный блок if отправляеть данные из amoCrm в Google Sheets
if (!empty($_POST['leads']['status'])) {
    require __DIR__ . '/app/fromAmoToSheets.php';
    $post = $_POST;
    $fromAmoToSheets = new fromAmoToSheets;
    $fromAmoToSheets->toSheets($post);
}

$raw = file_get_contents('php://input');
if (!empty($raw)){
    require __DIR__ . '/app/fromSheetsToAmo.php';
    $data =  json_decode($raw, true);
    $fromSheetsToAmo = new fromSheetsToAmo;
    $STA = $fromSheetsToAmo->toAmo($data);
}

if (!empty($_POST['leads']['update']['0'])) {
    require __DIR__ . '/app/updateGoogleSheets.php';
    $post = $_POST;
    $updateGoogleSheets = new updateGoogleSheets;
    $updateGoogleSheets->updateGS($post);
}

function log_data($data)
{
    $file = __DIR__ . '/log.txt';
    file_put_contents($file, var_export($data, true), FILE_APPEND);
}