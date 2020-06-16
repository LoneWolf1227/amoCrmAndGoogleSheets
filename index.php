<?php

require_once __DIR__ . '/libs/hamtim-amocrm.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

//Подключаемя к amoCRM
$amo = new HamtimAmocrm(AMO_LOGIN, AMO_API, AMO_DOMAIN);

//Данный блок if отправляеть данные из amoCrm в Google Sheets
if (!empty($_POST['leads']['status'])) {

    //Берём данные по ИД сделки
    $leadById = '/api/v4/leads/'.$_POST['leads']['status'][0]['id'].'?with=contacts';
    $lead = $amo->q($leadById, 'GET');
    //log_data($lead);

    //Берём данные контактов по ИД которая привязана к Сделку
    $contactsPath = '/api/v4/contacts/'.$lead->_embedded->contacts[0]->id;
    $contacts = $amo->q($contactsPath,'GET');
    //log_data($contacts);

    //Берём данные компании по ИД которая привязана к Сделку
    $companyPath = '/api/v4/companies/'.$lead->_embedded->companies[0]->id;
    $company = $amo->q($companyPath,'GET');
    //log_data($company);

    //Берём все нужные данные на соответствуюшие переменные
    $leadId = $lead->id;
    $leadName = $lead->name;
    $leadPrice = $lead->price;
    $leadData = date("d/m/y", $lead->created_at);
    $companyName = $company->name;

    /*Проверка если адрес задан в контакты тогда присваиваем его в
     * $address
     * Если адрес не задан в контакты тогда вазмём адрес Компании
     * Если и адрес компании не указана тогда оставим переменную
     * $address пустым
    */
    if ($contacts->custom_fields_values['0']->values['0']->value){
        $address = $contacts->custom_fields_values['0']->values['0']->value;
    }
    elseif($company->custom_fields_values['1']->values['0']->value){
        $address = $company->custom_fields_values['1']->values['0']->value;
    }else{
        $address = '';
    }

    //Подключаемся к Google Sheets Api по функции serviceClient()
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



$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
//log_data($data);

//Двнный блок if отправляеть данные изменённые в Google Sheets в amoCrm
if (!empty($data['ID_сделки'])){
    //Берём данные по ИД сделки
    $leadById = '/api/v4/leads/'. $data['ID_сделки'].'?with=contacts';
    $lead = $amo->q($leadById, 'GET');
    //log_data($lead);

    $update = array(
        'price' => $data['Сумма_сделки'],
        'name' => $data['Название_сделки']
    );

    //создаем новую сделку
    $path = '/api/v4/leads/'. $data['ID_сделки'];
    $leadAnswer = $amo->q($path, 'PATCH', $update);
    //log_data($leadAnswer);

    $companyUpdate = array(
        'name' => $data['Компания']
    );

    $companyPath = '/api/v4/companies/'.$lead->_embedded->companies[0]->id;
    $companyAnswer = $amo->q($companyPath, 'PATCH', $companyUpdate);
    //log_data($companyAnswer);


    //Берём данные контактов по ИД которая привязана к Сделку
    $contactsPath = '/api/v4/contacts/'.$lead->_embedded->contacts[0]->id;
    $contacts = $amo->q($contactsPath,'GET');

    $contactUpdate = array(
            'id' => $contacts->id,
            'custom_fields_values' =>
                array (
                    0 =>
                        array(
                            'field_id' => $contacts->custom_fields_values['0']->field_id,
                            'values' =>
                                array (
                                    0 =>
                                        array(
                                            'value' => $data['Адрес_объекта'],
                                        ),
                                ),
                        ),
                )

    );

    $contactsPath = '/api/v4/contacts/'.$contacts->id;
    $contactsAnswer = $amo->q($contactsPath, 'PATCH', $contactUpdate);
    //log_data($contactsAnswer);
}


//Фукцыя подключения к Google Sheets Api
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
