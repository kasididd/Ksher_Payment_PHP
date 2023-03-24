<?php

include_once 'ksher_pay_sdk.php';
$appid='mchไอดีร้าน';
$privatekey=<<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICYAIBAAKBgQCTeo9KFrjfotyp+Tzis13n4s/rDbkpo5LvOKH4gJlhft6Jk9Ty
GRqtvml78kpvGShkG0ofQBr72F6yPubh78vwhgF+ggebSa49WoIBH5Pfrl+HZQlX
oAv9FWAlLao9OgE9qTUhs1FKSIRUEya47+ZCAvmytp8UKpAodIFV1xmi4wIDAQAB
AoGBAI1z0Tnjv2Co6fOR3gcmuRwv3PL7z6yTetw+OoSKfBMaR8I3a9jloUQiTTSX
+TLEM4yI7Dg7GWaTaoiWcbQ1gStGaJJntCuqAZdWfgBd1ic5n3BDGUVezUI9CMAc
rMsFCYkuZnLimY40eXzaSqtlgDRVBo7ncMps+cv+y3AY7kyhAkUA2+utfVPR2XhN
qT3GreFLMxDkIls2UArEyVcJdt1X4RhJWSY9Adx5NXM1fUObRdP86E14Ruue7ELr
MoLNHd862tWuO5MCPQCrrG/RFykCxyagkZ9jrMWWQzoMcet4rW2K6+W2E4nILA5G
PgaX9vLAPwJ0hakmeZ5AHCnQy25Md7i1rXECRBES2wU/3KrljCH3idU2CICObye7
rRvJuj1nZHS1+nyfRhKFxXa/hyl/KEfxV7y0GI5wdUYMROpSx6/EN+h5zDfxHvXB
Ajw5kDViH+jxdIOgPZP7YRhTvTD+sUgqi8R6W4UH219MznDu3qdpVzWofrg9CABi
1U7Z4lenGj9expoZ2RECRC3Q8lj6SAWzmAEvUPOcp4zwPMD36vPKRWu1fHVijbpc
69XFNtZ9rKO7cAOt1fvAv3dadAzlr8DaicDdCx+pXGo4GsfE
-----END RSA PRIVATE KEY-----

EOD;
$in = file_get_contents("php://input");
$j= json_decode($in, true);
print_r($j);
print_r($in);
$time = date("Y-m-d H:i:s", time());
$class = new KsherPay($appid, $privatekey);

//1.การรับข้อมูลพารามิเตอร์ (Receive Parameters)
$input = file_get_contents("php://input");
tempLog("------notify data ".$time." begin------" );

$query = urldecode($input);
if( !$query){
    tempLog("NO RETURN DATA" );
    echo json_encode(array('result'=>'FAIL',"msg"=>'NO RETURN DATA'));
    exit;
}

//2.การตรวจสอบความถูกต้องของพารามิเตอร์ (Validate Parameters)
$data_array = json_decode($query,true);
tempLog("notify data :".json_encode( $data_array) );
if( !isset( $data_array['data']) || !isset( $data_array['data']['mch_order_no']) || !$data_array['data']['mch_order_no']){
    tempLog("notify data FAIL" );
    echo json_encode(array('result'=>'FAIL',"msg"=>'RETURN DATA ERROR'));
    exit;
}
//3.การดำเนินการในการประมวลผลคำสั่งซื้อ (Process Order)
if( array_key_exists("code", $data_array)
    && array_key_exists("sign", $data_array)
    && array_key_exists("data", $data_array)
    && array_key_exists("result", $data_array['data'])
    && $data_array['data']["result"] == "SUCCESS"){
    //3.การตรวจสอบความถูกต้องของลายเซ็นดิจิทัล (Digital Signature Validation)
    $verify_sign = $class->verify_ksher_sign($data_array['data'], $data_array['sign']);
    tempLog("IN IF function sign :". $verify_sign );
    if( $verify_sign==1 ){
        //การอัพเดทข้อมูลของคำสั่งซื้อ (Update Order Information) change order status
        //....
        tempLog('change order status');
        echo json_encode(array('result'=>'SUCCESS',"msg"=>'OK'));
    } else {
        tempLog('VERIFY_KSHER_SIGN_FAIL');
        echo json_encode(array('result'=>'Fail',"msg"=>'VERIFY_KSHER_SIGN_FAIL'));
    }
}
//4.การส่งข้อมูลกลับให้ผู้ใช้งาน (Return Information)
tempLog("------notify data ".$time." end------" );



function tempLog( $string ){
    if( !$string ) return false;
    $file = dirname(__FILE__)."/notify_log_".date("Ymd").".txt";
    $handle = fopen( $file, 'a+');
    fwrite( $handle , $string."\r");
    fclose( $handle );
}
