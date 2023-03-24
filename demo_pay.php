<?php
include_once 'ksher_pay_sdk.php';
$appid='mchไอดีร้าน';
$privatekey=<<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICYQIBAAKBgQCgETzM/7lu+2n7OGYsVTqVI5K0EHOfT73vG6Ascelu7+spOH0J
E14GwyGNhwM05ohHOrOH8H7fA71xqM/u1WPBnPr89youCKwfVFE9dkar6Fso59sr
gW1ZZ7Mf28Ly5rEuq8fTZjWyDT0ti6e1VAMr34qWGNe1a+wTBpdPAnd/KwIDAQAB
AoGAWukvZScgxJ3alap0rV1Cxo3LtqVZZfQ+Zd2E3XldIXr2TxUcPtlXH/QXzHAQ
LC163SnD6cN362YOZM2B2WNjf1x3C84y7gKNYtygR2ggnQOHRMHWvUN8BdtGkWB8
jM917GUXZq4aLjXkIgO8t/G7defPNWwFJqjhiEElacuO6gECRQCs82zX6SsTJ4T7
fbcVDvk+FGpYw8vSWuJpFerZZKIJc78L6AM8KRU0TXqsGHjy+zTxEo2eU/bPLY3k
Nclza9qQQGPaqwI9AOzuDwaqKZdoyVBs61X9gUT4U1Gv3xuCcm2y/+0ZUi8mWx8a
zwUEU/YQLpBBI7ePU1DXbbudrI19PCLtgQJEOYOR9JMjsfD4djGuSqB6HjznyLED
/OYgWNXjDXw7rm0BYVI8kSsQVB5X5xcUUalcR7blxXsinm8Fuphwb6O0QThIfJUC
PQCehQh4pRD+xJUswjMSbXI3+w2D8e+MMFLvInwo3nAmK7t7rCwmZybl0x3UVkDX
Z4WO6RP6Gxx/7fJ2TAECRQCIobt0JksMdVGvPbliOdhTs7aeffdja73b7Eg3MGU3
omX3S4bYWgSTVG8rDyVoAFprJE8hKL/4LTTW4ChOTmBzmzSJFw==
-----END RSA PRIVATE KEY-----

EOD;

set_time_limit(0);
$class = new KsherPay($appid, $privatekey);
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'native_pay') {
	echo "<br />---------<br />native_pay:<br />";
	$native_pay_data = array(
		"mch_order_no" => $_POST['mch_order_no'],
		"total_fee" => round($_POST['total_fee'], 2) * 100,
		"fee_type" => $_POST['fee_type'],
		"channel" => $_POST['channel'],
		"notify_url" => "https://53b8-27-55-94-65.ap.ngrok.io/php/notify_test.php", //แจ้งเตือนเมื่อจ่ายสำเร็จ
		);

	$native_pay_response = $class->native_pay($native_pay_data);
	$native_pay_array = json_decode($native_pay_response, true);
	echo "<h1>ค่าที่ส่งไป</h1>";

	if (isset($native_pay_array['code']) && $native_pay_array['code'] == 0 && $native_pay_array['data']['imgdat']) {
		echo "<h2> Successfully Create C Scan B Order</h2>";
		echo "<p>Please scan QR code:<p>";
		echo "\n<img src='" . $native_pay_array['data']['imgdat'] . "'alt='payment qr code'>\n";
	} else {
		echo "<h2> Fail to Create C Scan B Order</h2>";
		echo "<p1> Here's the raw response :</p1>";
	print_r($native_pay_array);
	}
	exit;	
} else if ($action == 'gateway_pay') {
	echo "<br />gateway_pay<br />";
	$gateway_pay_data = array(
		'mch_order_no' => $_POST['mch_order_no'],
		"total_fee" => round($_POST['total_fee'], 2) * 100,
        "fee_type" => $_POST['fee_type'],
		"channel_list" => 'promptpay,card',
        'mch_code' => $_POST['mch_order_no'],
        'mch_redirect_url' => 'https://53b8-27-55-94-65.ap.ngrok.io/php',//นำทางไปหน้าที่เราต้องการ เมื่อชำระเงินสำเร็จ
        'mch_redirect_url_fail' => 'https://53b8-27-55-94-65.ap.ngrok.io/php/fail.php', // หากการชำระไม่สำเร็จ
		'product_name' => $_POST['product_name'],
        'refer_url' => 'http://www.ksher.cn',
		"mch_notify_url" => "https://53b8-27-55-94-65.ap.ngrok.io/php/notify_test.php", // callback
		'device' => '', // responsesivee
		'member_id' => 'pjaspjfpo212312'// รหัสผู้ใช้เพื่อใช้เก็บ บัตร 
	);

    $gateway_pay_response = $class->gateway_pay($gateway_pay_data);
    $gateway_pay_array = json_decode($gateway_pay_response, true);

	if (isset($gateway_pay_array['data']['pay_content'])) {
		echo $gateway_pay_array['data']['pay_content'];
		echo "<h2> Successfully Create Redirect Order</h2>";
		echo '<a href=' . $gateway_pay_array['data']['pay_content'] . '>enter link to pay</a>';
	} else {
		echo "<h2> Fail to create Redirect Order</h2>";
		echo "<p1> Here's the raw response </p1>";
		echo $gateway_pay_response;
    }
    exit();
} else{
	echo "not select";
	exit();
}