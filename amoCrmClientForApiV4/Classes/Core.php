<?php


namespace Classes;

use libs;

class Core
{
    public $amo;

    public function __construct()
    {
        //Доступ к amoCRM
        $AMO_DOMAIN = 'manalonewolf';
        $AMO_API ='7adc591a56a7747af9a257f15be0d32404279948';
        $AMO_LOGIN ='manalonewolf@gmail.com';
        $this->amo = new libs\hamtimAmocrm($AMO_LOGIN, $AMO_API, $AMO_DOMAIN);
    }
}