<?php

require_once __DIR__ . '/libs/hamtim-amocrm.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

if (!empty($_POST['leads']['status'])) {

    #Подключаемя к amoCRM
    $amo = new HamtimAmocrm(AMO_LOGIN, AMO_API, AMO_DOMAIN);

    #Берём данные по ИД сделки
    $leadById = '/api/v4/leads/'.$_POST['leads']['status'][0]['id'];
    $lead = $amo->q($leadById);

    #Берём данные компании по ИД которая привязана к Сделку
    $companyPath = '/api/v4/companies/'.$lead->_embedded->companies[0]->id;
    $company = $amo->q($companyPath);

    #Берём все нужные данные на соответствуюшие переменные
    $leadId = $lead->id;
    $leadName = $lead->name;
    $leadPrice = $lead->price;
    $leadData = date("d/m/y", $lead->created_at);
    $companyName = $company->name;
    $address = $company->custom_fields_values['1']->values['0']->value;

    #Подключаемся к Google Sheets Api по функции serviceClient()
    $service = serviceClient();
    $spreadsheetId = '1wIaw9B41IwVbkjkeEejxtSKVK-X0AXusWnJsgUFK6UQ';
    $range = "Sheet1";
    $values = [
        [$leadId,$leadData,$leadName,$companyName,$address,$leadPrice]
    ];
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => 'RAW'
    ];
    $insert = [
        'insertDataOptions' => 'INSERT_ROWS'
    ];
    $result = $service->spreadsheets_values->append(
        $spreadsheetId,
        $range,
        $body,
        $params,
        $insert
    );
}

#Фукцыя подключения к Google Sheets Api
function serviceClient(){
    $client = new \Google_Client();
    $client->setApplicationName('Amocrm and Sheets');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    return new Google_Service_Sheets($client);
}
