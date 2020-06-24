<?php

class Core
{
    private $values;
    public function findRowByValueInSheets($value)
    {
        $spreadsheetId = '1wIaw9B41IwVbkjkeEejxtSKVK-X0AXusWnJsgUFK6UQ';
        $service = $this->serviceClient();
        $this->values;
        $row = 1;
        do {
            $range = "Sheet1!A" . $row;
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $this->values = $response->getValues();
            ++$row;
            if (empty($this->values['0']['0']) || empty($this->values) || empty($this->values['0'])) {
                break;
            }
        } while ($this->values['0']['0'] !== $value);
        if ($this->values['0']['0'] === $value){
            return $row - 1;
        }
        else{
            return false;
        }
    }

    public function serviceClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Amocrm and Sheets');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(__DIR__ . '/../credentials.json');
        return new Google_Service_Sheets($client);
    }

    public function log_data($data)
    {
        $file = __DIR__ . '/../log.txt';
        file_put_contents($file, var_export($data, true), FILE_APPEND);
    }


}