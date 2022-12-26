<?php

namespace App\Traits;



trait HelperTrait
{
     public function generatePaymentReference(){
        //generate  a payment reference based on the current timestamp and a random number
        $payment_reference = 'ADFA'.date('YmdHis').rand(1000,9999);
        return $payment_reference;

     }

}
