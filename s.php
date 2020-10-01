<?php

$url = "https://walletmc.ipay.ua";
$test_phone = "380639429219";
$login = "batyevkanet";


function check() {


}


function list() {


}


function paymentCreate() {


}

function get_sign(){

	$sign_key = "198f4899f0c4964595c7514ae451899b91fe8baa";
	$date = date('Y-m-d');
	$time = date('h:i:s');
	$date_time = $date." ".$time;
	return $sign = hash("sha512", $date_time.$sign_key);

}


function createWidget(){

$json_req = '
{

    "request": {

        "auth": {

            "login": "'.$login.'",

            "time": "'.$date_time.'",

            "sign": "'.$sign.'"

        },

        "action": "InitWidgetSession",

        "body": {

            "msisdn": "380639429219",

            "user_id": "99",

            "pmt_desc": "Сплата за послугу: Інтернет; Номер договору: 99; Сума: 1.00 грн.",

            "pmt_info": {

                "invoice": 100

            }

        }

    }

}



';

}

/*

$options = array(

    'http' => array(

        'header'  => "Content-type: application/json\r\n",

        'method'  => 'POST',

        'content' => $json_req

    )

);

$context  = stream_context_create($options);

$result = file_get_contents($url, false, $context);

if ($result === FALSE) { /* Handle error }

*/ 

//var_dump($result);



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
    echo "cURL error ({$errno}):\n {$error_message}";
}

$response = json_decode($re, true);

return $response

}
$session = $response["response"]["session"];
?>

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
                partner: '<?php echo $login ?>',
                lang: 'ru', 
                session: '<?php echo $session ?>'
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
