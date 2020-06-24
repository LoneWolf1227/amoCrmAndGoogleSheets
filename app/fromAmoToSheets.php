<?php

class fromAmoToSheets extends Core
{
    public function toSheets($post)
    {
        $get = new Classes\Get();

            //Берём данные по ИД сделки
            $id = $post['leads']['status'][0]['id'];
            $row = $this->findRowByValueInSheets($id);

            if ($row === false) {
                //Берём данные контактов по ИД которая привязана к Сделку
                $id = $post['leads']['status'][0]['id'];
                $lead = $get->leadById($id, 'contacts');
                $contacts = $get->contactById($lead->_embedded->contacts[0]->id, 'contacts');

                //Берём данные компании по ИД которая привязана к Сделку
                $company = $get->companyById($lead->_embedded->companies[0]->id);

                //Берём все нужные данные на соответствуюшие переменные
                $leadId = $lead->id;
                $leadName = $lead->name;
                $leadPrice = $lead->price;
                $leadData = date("d/m/y", $lead->created_at);
                $companyName = $company->name;

                if ($contacts->custom_fields_values['0']->values['0']->value) {
                    $address = $contacts->custom_fields_values['0']->values['0']->value;
                } elseif ($company->custom_fields_values['1']->values['0']->value) {
                    $address = $company->custom_fields_values['1']->values['0']->value;
                } else {
                    $address = '';
                }

                //Подключаемся к Google Sheets Api по функции serviceClient()
                $service = $this->serviceClient();
                $spreadsheetId = '1wIaw9B41IwVbkjkeEejxtSKVK-X0AXusWnJsgUFK6UQ';
                $range = "Sheet1";
                $values = [
                    [$leadId, $leadData, $leadName, $companyName, $address, $leadPrice]
                ];
                $this->log_data($values);
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
    }
}
