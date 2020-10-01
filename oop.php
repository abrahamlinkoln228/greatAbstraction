//hello here is the diference 
<?php

class MasterPass(){
    const $url = "https://walletmc.ipay.ua";
    const $login = "batyevkanet";
    const $smch_id = "6827"; //or 6828
    $test_phone = "380639429219";
    $client_id = "99";
    $sum = 1;
    $payment_sum = $sum * 100; //in coins
    $date = date('Y-m-d');
    $time = date('h:i:s');
    $date_time = $date." ".$time;
    $sign = get_sign($date_time);
  
    
    function __construct($url, $login, $smch_id, $) {
        $this->url = $url;
    }
    
    //$response = xCurlJson(createWidget($login, $date_time, $sign), $url);
    //$session = $response["response"]["session"];
    //getWidgetHtml($login, $session);
    
    
    $check_result = xCurlJson(check($login, $date_time, $sign, $client_id, $test_phone), $url);
    $check_result = (array)$check_result;
    $user_status = $check_result["response"]["user_status"];
    
    pay_cron($user_status, $login, $date_time, $sign, $client_id, $url, $test_phone, 100, $smch_id, "11111111111111111111111111111111");
    
    function pay_cron($user_status, $login, $time, $sign, $user_id, $url, $phone, $payment_sum, $smch, $guid) {
    
        if($user_status == "exists"){
            $cards = xCurlJson(listCards($login, $time, $sign, $user_id, $phone), $url);
        }
    
    
            foreach($cards['response'] as $resp) {
                $card = $resp['card_alias'];
                $pay = xCurlJson(paymentCreate($login, $time, $sign, $user_id, $payment_sum, $card, $smch, $guid, $phone), $url);
                if($pay['response']['pmt_status'] == "5"){
                    //write2db;
                }
            }
    
    }
    
    function check($login, $time, $sign, $user_id, $phone) {
        
        $req = '
        
        {
        "request": {
            "auth": {
                "login": "'.$login.'",
                "time": "'.$time.'",
                "sign": "'.$sign.'"
            },
            "action": "Check",
            "body": {
                "msisdn": "'.$phone.'",
                "user_id": "'.$user_id.'"
            }
        }
    }
    
        ';
    
        return $req;
    }
    
    
    function listCards($login, $time, $sign, $user_id, $phone) {
        
        $req = '
        
        {
        "request": {
            "auth": {
                "login": "'.$login.'",
                "time": "'.$time.'",
                "sign": "'.$sign.'"
            },
            "action": "List",
            "body": {
                "msisdn": "'.$phone.'",
                "user_id": "'.$user_id.'"
            }
        }
    }
    
        ';
    
    return $req;
        
    }
    
    
    function paymentCreate($login, $time, $sign, $user_id, $payment_sum, $card, $smch, $guid, $phone) {
        
        $req = '
        
        {
        "request": {
            "auth": {
                "login": "'.$login.'",
                "time": "'.$time.'",
                "sign": "'.$sign.'"
            },
            "action": "PaymentCreate",
            "body": {
                "msisdn": "'.$phone.'",
                "user_id": "'.$user_id.'",
                "invoice": '.$payment_sum.',
                "card_alias": "'.$card.'",
                "transactions": [
                    {"invoice":'.$payment_sum.',"smch_id":"'.$smch.'","desc":"Тестовий платіж","info":{"custom_field_1":"test"}}
                ],
                "guid": "'.$guid.'"
            }
        }
    }
    
    
        ';
    
    return $req;
    }
    
    function get_sign($date_time){
    
        $sign_key = "198f4899f0c4964595c7514ae451899b91fe8baa";
        return $sign = hash("sha512", $date_time.$sign_key);
    
    }
    
    
    function createWidget($login, $time, $sign){
    
    $json_req = '
    {
        "request": {
            "auth": {
                "login": "'.$login.'",
                "time": "'.$time.'",
                "sign": "'.$sign.'"
            },
            "action": "InitWidgetSession",
            "body": {
                "msisdn": "380639429219",
                "user_id": "99",
                "pmt_desc": "Сп
лата за послугу: Інтернет; Номер договору: 99; Сума: 1.00 грн.",
                "pmt_info": {
                    "invoice": 100
                }
            }
        }
    }
    ';
    
    return $json_req;
    }
    
    
    
    function xCurlJson($json_req, $url){
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_req);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_req))
        );
    
        $re = curl_exec($ch);
        
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo
    "cURL error ({$errno}):\n {$error_message}";
        }
        
        $response = json_decode($re, true);
        return $response;
    
    }
    
    function getWidgetHtml($login, $session){
        echo $html = <<<EOF
    
                    <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta content="width=device-width, initial-scale=1.0" name="viewport">
                    <title>iPay Masterpass Widget</title>
                    <script type="text/javascript" src="https://widgetmp.ipay.ua/widget.js"></script>
                </head>
                <body>
                <a href="#" onclick="MasterpassWidget.open(
                            {
                                partner: '$login',
                                lang: 'ru', 
                                session: '$session'
                            },
                            // функція, яка буде виконана після закриття віджету
                function() {},
                            // функція, яка буде виконана після успішної оплати
                            function() {},
                            // функція, яка буде виконана після помилкової оплати
                            function() {}
                );">
                    <img src="https://widgetmp.ipay.ua/mp-button.svg">
                </a>
                </body>
                </html>
    
    
    EOF;
    
    }
    
}
