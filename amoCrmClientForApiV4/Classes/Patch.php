<?php


namespace Classes;


class Patch extends Core
{
    public function patchItemById($id, $item, $fields)
    {
        $path = '/api/v4/'.$item.'/'.$id;
        return $this->amo->q($path, 'PATCH', $fields);
    }

    public function contactById($id, $fields){
        return $this->patchItemById($id, 'contacts', $fields);
    }

    public function companyById($id, $fields){
        return $this->patchItemById($id, 'companies', $fields);
    }

    public function leadById($id, $fields){
        return $this->patchItemById($id, 'leads', $fields);
    }
}