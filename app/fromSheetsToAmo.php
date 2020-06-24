<?php

class fromSheetsToAmo extends Core
{
    function toAmo($data)
    {

        $get = new Classes\Get();
        $patch = new Classes\Patch();

        //Двнный блок if отправляеть данные изменённые в Google Sheets в amoCrm
        if (!empty($data['ID_сделки'])) {

            //Берём данные по ИД сделки
            $lead = $get->leadById($data['ID_сделки'], 'contacts');
            //log_data($lead);

            $fields = array(
                'price' => $data['Сумма_сделки'],
                'name' => $data['Название_сделки']
            );
            $leadAnswer = $patch->leadById($data['ID_сделки'], $fields);
            //log_data($leadAnswer);

            $fields = array(
                'name' => $data['Компания']
            );
            $companyAnswer = $patch->companyById($lead->_embedded->companies[0]->id, $fields);
            //log_data($companyAnswer);

            //Берём данные контактов по ИД которая привязана к Сделку
            $id = $lead->_embedded->contacts[0]->id;
            $contacts = $get->contactById($id, 'contacts');

            $fields = array(
                'id' => $contacts->id,
                'custom_fields_values' =>
                    array(
                        0 =>
                            array(
                                'field_id' => $contacts->custom_fields_values['0']->field_id,
                                'values' =>
                                    array(
                                        0 =>
                                            array(
                                                'value' => $data['Адрес_объекта'],
                                            ),
                                    ),
                            ),
                    )

            );
            $contactsAnswer = $patch->contactById($contacts->id, $fields);
            //log_data($contactsAnswer);
        }
    }
}
