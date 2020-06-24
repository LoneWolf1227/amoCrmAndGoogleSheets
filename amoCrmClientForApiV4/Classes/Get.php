<?php


namespace Classes;


class Get extends Core
{
    public function getItems($item, $customFields = '')
    {
        if (!empty($customFields))
        {
            $path =  '/api/v4/'.$item.'/'.$customFields;
        }
        else
        {
            $path =  '/api/v4/'.$item;
        }
        return $this->amo->q($path, 'GET');
    }

    public function getContacts($customFields = '')
    {
        return $this->getItems('contacts', $customFields);
    }

    public function getItemById($id, $item, $with = '')
    {
        if (!empty($with))
        {
            $path = '/api/v4/'.$item.'/'.$id.'?with='.$with;
        }
        else{
            $path = '/api/v4/'.$item.'/'.$id;
        }
        return $this->amo->q($path, 'GET');
    }

    public function leadById($id, $with = ''){
        return $this->getItemById($id, 'leads', $with);
    }

    public function contactById($id, $with = ''){
        return $this->getItemById($id, 'contacts', $with);
    }

    public function companyById($id, $with = ''){
        return $this->getItemById($id, 'companies', $with);
    }


}