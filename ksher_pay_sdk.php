<?php
class KsherPay
{
    public $time;
    public $appid; //ksher appid
    public $privatekey; // การใช้งานฟังก์ชันเกี่ยวกับเงิน จำเป็นต้องใช้กุญแจส่วนตัว (Private Key)
    public $pubkey; //Ksher จะมีกุญแจสาธารณะ (Public Key) สำหรับการยืนยันเซ็นต์ของเงินเพื่อให้แน่ใจว่าเงินถูกสร้างขึ้นโดย Ksher เท่านั้น 
    public $version; //สำหรับ SDK เวอร์ชั่นล่าสุดของ Ksher API สามารถดาวน์โหลดและติดตั้งได้จากหน้าเว็บไซต์ของ Ksher 
    public $pay_domain;
    public $gateway_domain;

    public function __construct($appid = '', $privatekey = '', $version = '3.0.0')
    {
        $this->time = date("YmdHis", time());
        $this->appid = $appid;
        $this->privatekey = $privatekey;
        $this->version = $version;
        $this->pay_domain = 'https://api.mch.ksher.net/KsherPay';
        $this->gateway_domain = 'https://gateway.ksher.com/api';

        $this->pubkey = <<<EOD
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAL7955OCuN4I8eYNL/mixZWIXIgCvIVE
ivlxqdpiHPcOLdQ2RPSx/pORpsUu/E9wz0mYS2PY7hNc2mBgBOQT+wUCAwEAAQ==
-----END PUBLIC KEY-----
EOD;
    }

    /**
     * สุ่มเลข
     */
    public function generate_nonce_str($len = 16)
    {
        $nonce_str = "";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < $len; $i++) {
            $nonce_str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $nonce_str;
    }
    /**
     * สร้างsign
     *  $data
     *  $private_key_content
     */
    public function ksher_sign($data)
    {
        $message = self::paramData($data);
        $private_key = openssl_get_privatekey($this->privatekey);
        openssl_sign($message, $encoded_sign, $private_key, OPENSSL_ALGO_MD5);
        openssl_free_key($private_key);
        $encoded_sign = bin2hex($encoded_sign);
        return $encoded_sign;
    }
    /**
     * การยืนยันลายเซ็นต์ (การตรวจสอบความถูกต้องของลายเซ็นต์)
     */
    public function verify_ksher_sign($data, $sign)
    {
        $sign = pack("H*", $sign);
        $message = self::paramData($data);
        $res = openssl_get_publickey($this->pubkey);
        $result = openssl_verify($message, $sign, $res, OPENSSL_ALGO_MD5);
        openssl_free_key($res);
        return $result;
    }
    /**
     * การประมวลผลข้อมูลที่ต้องการเข้ารหัส
     */
    private static function paramData($data)
    {
        ksort($data);
        $message = '';
        foreach ($data as $key => $value) {
            $message .= $key . "=" . $value;
        }
        $message = mb_convert_encoding($message, "UTF-8");
        return $message;
    }
    /**
     * @access การร้องขอข้อมูลด้วยวิธี "get"
     * @params url //ที่อยู่ของคำร้องขอ (หรือเว็บเซอร์วิส)
     * @params data //ข้อมูลที่ร้องขอ, ในรูปแบบของอาร์เรย์ (array)
     * */
    public function _request($url, $data = array())
    {
        try {
            $data['sign'] = $this->ksher_sign($data);
            $queryData=http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $output = curl_exec($ch);

            if ($output !== false) {
                $response_array = json_decode($output, true);
                if ($response_array['code'] == 0) {
                    if (!$this->verify_ksher_sign($response_array['data'], $response_array['sign'])) {
                        $temp = array(
                            "code" => 0,
                            "data" => array(
                                "err_code" => "VERIFY_KSHER_SIGN_FAIL",
                                "err_msg" => "verify signature failed",
                                "result" => "FAIL"
                            ),
                            "msg" => "ok",
                            "sign" => "",
                            "status_code" => "",
                            "status_msg" => "",
                            "time_stamp" => $this->time,
                            "version" => $this->version
                        );
                        return json_encode($temp);
                    }
                }
            }
            curl_close($ch);
            return $output;
        } catch (Exception $e) {
            echo 'curl error';
            return false;
        }
    }
    public function native_pay($data)
    {
        $data['appid'] = $this->appid;
        $data['nonce_str'] = $this->generate_nonce_str();
        $data['time_stamp'] = $this->time;
        $response = $this->_request($this->pay_domain . '/native_pay', $data);
        return $response;           
    }
    public function gateway_pay($data)
    {
        $data['appid'] = $this->appid;
        $data['nonce_str'] = $this->generate_nonce_str();
        $data['time_stamp'] = $this->time;
        $data['shop_name'] = "ชื่อร้าน";
        $response = $this->_request($this->gateway_domain . '/gateway_pay', $data);
        return $response;
    }

}
