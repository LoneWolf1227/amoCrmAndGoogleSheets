<?php

class updateGoogleSheets extends Core
{
    public function updateGS($post)
    {
            $row = $this->findRowByValueInSheets($post['leads']['update']['0']['id']);
            if ($row !== false)
            {
                $spreadsheetId = '1wIaw9B41IwVbkjkeEejxtSKVK-X0AXusWnJsgUFK6UQ';
                $service = $this->serviceClient();
                $range = "Sheet1!C" . $row . ':F' . $row;
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $values = $response->getValues();

                $get = new Classes\Get();
                $lead = $get->leadById($post['leads']['update']['0']['id'], 'contacts');

                $contacts = $get->contactById($lead->_embedded->contacts[0]->id, 'companies');
                //$this->log_data($contacts);
                $company = $get->companyById($lead->_embedded->companies[0]->id);

                if ($contacts->custom_fields_values['0']->values['0']->value) {
                    $address = $contacts->custom_fields_values['0']->values['0']->value;
                } elseif ($company->custom_fields_values['1']->values['0']->value) {
                    $address = $company->custom_fields_values['1']->values['0']->value;
                } else {
                    $address = '';
                }

                $values = array(
                    array($lead->name, $company->name, $address, $lead->price)
                );
                $body = new Google_Service_Sheets_ValueRange(array(
                    'values' => $values
                ));
                $params = array(
                    'valueInputOption' => 'RAW'
                );
                $result = $service->spreadsheets_values->update(
                    $spreadsheetId,
                    $range,
                    $body,
                    $params
                );
            }

    }
}