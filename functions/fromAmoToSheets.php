<?php

function fromAmoToSheets(){
    require_once __DIR__ . '/../config.php';
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE,
        DB_LOGIN,
        DB_PASS
    );

    $get = new Classes\Get();

    $massage = '';

    //Данный блок if отправляеть данные из amoCrm в Google Sheets
    if (!empty($_POST['leads']['status'])) {

        //Берём данные по ИД сделки
        $id = $_POST['leads']['status'][0]['id'];
        $lead = $get->leadById($id, 'contacts');
        //$massage .= var_export($lead, true);

        /* Добавляем в БД id лида который был отправлен в Google Sheets
         * id лида в база отмечен ка unique index
         * при новом добавлении проверяется если id существует
         * тогда функция отправит false это означает что в Google Sheets
         * ест такой лид и лид не будить дублироваться.
        */
        $sql = "INSERT INTO `leads`(`lead_id`) VALUES ($lead->id)";
        $stmt = $pdo->query($sql);

        if ($stmt) {
            //Берём данные контактов по ИД которая привязана к Сделку
            $id = $lead->_embedded->contacts[0]->id;
            $contacts = $get->contactById($id, 'contacts');
            //$massage .= var_export($contacts, true);

            //Берём данные компании по ИД которая привязана к Сделку
            $id = $lead->_embedded->companies[0]->id;
            $company = $get->companyById($id);
            //$massage .= var_export($company, true);

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
            if ($contacts->custom_fields_values['0']->values['0']->value) {
                $address = $contacts->custom_fields_values['0']->values['0']->value;
            } elseif ($company->custom_fields_values['1']->values['0']->value) {
                $address = $company->custom_fields_values['1']->values['0']->value;
            } else {
                $address = '';
            }

            //Подключаемся к Google Sheets Api по функции serviceClient()
            $service = serviceClient();
            $spreadsheetId = '1wIaw9B41IwVbkjkeEejxtSKVK-X0AXusWnJsgUFK6UQ';
            $range = "Sheet1";
            $values = [
                [$leadId, $leadData, $leadName, $companyName, $address, $leadPrice]
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
            //$massage .= var_export($result, true);
        }
    }

    return $massage;
}
