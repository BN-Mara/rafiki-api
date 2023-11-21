<?php

namespace App\Helper;

use App\Entity\Payment;


class PaymentUrl{
    public function paymentUrl(Payment $payment, string $host): string{
        
        $postData = ["PayType"=>"MaxiCash","Amount"=>$payment->getAmount() * 100,"Currency"=>$payment->getCurrency(),
        "Telephone"=>$payment->getPhone(),"Email"=>$payment->getEmail(),"MerchantID"=>"4bc27182c02e4d159f1179d10b3537af","MerchantPassword"=>"deaea1d899d24f9ea47b175ce8d1a4ae",
        "Language"=>"fr","Reference"=>$payment->getReference(),"Accepturl"=>$host."/vote/process/success",
        "Cancelurl"=>$host."/vote/process/cancel","Declineurl"=>$host."/vote/process/fail",
        "NotifyURL"=>$host."/vote/process/notify"];
        $jsonData = json_encode($postData);
        $maxiUrl = 'https://api.maxicashapp.com/payentry?data='.$jsonData;
        return $maxiUrl;
    }
}