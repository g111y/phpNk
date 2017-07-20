<?php
namespace src;

//require "../baiduAi/AipOcr.php";
use baiduAi\AipOcr;

class login
{
    private $APP_ID = '9851355';
    private $API_KEY = 'gXQf5uKNso6FFgPa7yKMNjWq';
    private $SECRET_KEY = 'CBUiRZyQxDtwyUmq8axaaAkvHiWs4YUN';
    private $loginUrl = "http://retail.belle.net.cn/be_ready_login";
    private $loginName = "NKMGY1";
    private $loginPassword = "NKMGY1";
    private $captImgUrl = "http://retail.belle.net.cn/createVerifyCode?sessionKey=naviVerifyCode&time=";
    private $headers = array(
        "Host" => "retail.belle.net.cn",
        "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:53.0) Gecko/20100101 Firefox/53.0",
    );

    public function getCapImg()
    {
        $headers = array(
            "Host" => "retail.belle.net.cn",
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:53.0) Gecko/20100101 Firefox/53.0",
            "Content-Type" => "image/png",
        );
        $cookie_jar = __DIR__ . "/cookie.txt";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $this->captImgUrl . time());
        $ret = curl_exec($curl);
        curl_close($curl);
        $fp = fopen("1.jpg", 'wb');
        fwrite($fp, $ret);
        fclose($fp);
    }

    private function getCaptNo()
    {
        $this->getCapImg();

        $data = new \stdClass();
        $apiOcr = new AipOcr($this->APP_ID, $this->API_KEY, $this->SECRET_KEY);

        $result = $apiOcr->webImage(file_get_contents("1.jpg"));
        if ($result) {
            $data->success = true;
            $data->captNo = $result['words_result'][0]['words'];
            return $data;
        } else {
            $data->success = false;
            $data->captNo = "无法识别验证码!";
            return $data;
        }
    }

    public function startLogin()
    {
        $capt = $this->getCaptNo();
        if (!$capt->success) {
            print_r($capt);
            return;
        }
        print_r($capt);
        $formData = array(
            "loginName" => $this->loginName,
            "loginPassword" => $this->loginPassword,
            "flag" => "submit",
            "cookieFlag" => "0",
            "vcode" => $capt->captNo,
            "systemType" => "1",
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
        curl_setopt($curl, CURLOPT_COOKIEJAR, __DIR__ . "/cookie.txt");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $this->loginUrl);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $formData);
        $ret = curl_exec($curl);
        // print_r($ret);
        curl_close($curl);
        $ret = json_decode($ret);

        return $ret;
    }

    public function getTopPage()
    {
        $cookie_jar = __DIR__ . "/cookie.txt";
        $curl = curl_init();
        $url = "http://retail.belle.net.cn/pos/sso_to_index";
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $ret = curl_exec($curl);
        curl_close($curl);
        //print_r($ret);
        print_r("登陆首页" . PHP_EOL);
    }
    
    public function test()
    {
        print_r("载入成功" . PHP_EOL);
    }
}
