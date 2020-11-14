<?php

function lipaNaMpesaPassword()
    {
        $lipa_time = gmdate('Ymdhis');
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = 174379;
        $timestamp =$lipa_time;

        $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);
        return $lipa_na_mpesa_password;
    }


    /**
     * Lipa na M-PESA STK Push method
     * */

    function customerMpesaSTKPush($user_phone,$user_amount)
    {
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.generateAccessToken()));


        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 174379,
            'Password' => lipaNaMpesaPassword(),
            'Timestamp' => gmdate('Ymdhis'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $user_amount,
            'PartyA' => $user_phone, // replace this with your phone number
            'PartyB' => 174379,
            'PhoneNumber' => $user_phone, // replace this with your phone number
            'CallBackURL' => 'https://fomis.kibucu.org/fomis/test.php',
            'AccountReference' => "FOMIS",
            'TransactionDesc' => "FOMIS"
        ];

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        return $curl_response;
    }


    function generateAccessToken()
    {
        $consumer_key="";
        $consumer_secret="";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);

        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        return $access_token->access_token;
    }
    $user_phone=254702822379;
    $user_amount=1;
    $response = customerMpesaSTKPush($user_phone,$user_amount);

      echo $response;
?>