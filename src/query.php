<?php
namespace src;

class query
{
    //商品资料查询
    private $listUrl = "http://retail.belle.net.cn/pos/common_item_info/query/list";
    private $listBarUrl = "http://retail.belle.net.cn/pos/item_sku/query/item_no?";
    private $headers = array(
        "Host" => "retail.belle.net.cn",
        "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:53.0) Gecko/20100101 Firefox/53.0",
        "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
        "Referer" => "http://retail.belle.net.cn/pos/common_item_info/list",
    );

    public function queryList($gdno)
    {
        $itemCodeSearch = $gdno;
        $formData = array(
            "itemCodeSearch" => $itemCodeSearch,
            "page" => "1",
            "rows" => "10",
            "pageNumber" => "1",
            "pageSize" => "10",
            "pageIndex" => "0",
            "orderby" => "asc",
        );
        $ret = $this->postCurl($formData, $this->listUrl);
        return $ret;
    }

    public function postCurl($formData, $url)
    {
        $cookie_jar = __DIR__ . "/cookie.txt";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
        $ret = curl_exec($ch);
        // print_r($ret);
        curl_close($ch);
        $ret = json_decode($ret);
        return $ret;
    }

    public function getCurl($url)
    {
        $cookie_jar = __DIR__ . "/cookie.txt";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        // print_r($ret);
        curl_close($ch);
        $ret = json_decode($ret);
        return $ret;
    }

    public function getBarCode($itemNo, $code, $colorName, $orderby = "asc")
    {
        $data = array(
            'itemNo' => $itemNo,
            'code' => $code,
            'colorName' => $colorName,
            'orderby' => $orderby,
        );
        $url = $this->listBarUrl . http_build_query($data);
        $ret=$this->getCurl($url);
        return $ret;
    }

    public function getImgFile($gdno,$barCode,$dirCount){
        $imgUrl="http://pic.belle.net.cn/2017/MDM/NK/{$gdno}.jpg";

        $headers = array(
            "Host" => "retail.belle.net.cn",
            "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:53.0) Gecko/20100101 Firefox/53.0",
            "Content-Type" => "image/png",
        );
        $cookie_jar = __DIR__ . "/cookie.txt";
        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $imgUrl);
        $ret = curl_exec($curl);
        curl_close($curl);
        if(!file_exists("images/image{$dirCount}")){
            mkdir("images/image{$dirCount}");
        }
        $fp = fopen("images/image{$dirCount}/{$barCode}.jpg", 'wb');
        fwrite($fp, $ret);
        fclose($fp);
    }
}
