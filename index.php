<?php

require 'vendor/autoload.php';

use src\gdno;
use src\login;
use src\query;
//print_r(AipImageUtil::getImageInfo(file_get_contents('http://retail.belle.net.cn/createVerifyCode?sessionKey=naviVerifyCode&time=1499409696')));

$login = new login;
$query = new query;
$gdnoFile = new gdno;
//$login->test();
$loginTime = file_get_contents("login.txt");

if ($loginTime <= time()) {
    print_r("登陆失效！，重新登陆" . PHP_EOL);
    $aa = $login->startLogin();
    if ($aa) {
        $fs = fopen("login.txt", "w");
        fwrite($fs, time() + 3600 * 3);
        fclose($fs);
    }
}

print_r("登陆有效!" . PHP_EOL);

$login->getTopPage();

$gdnoLists = $gdnoFile->gdnoImport("gdno.csv");
//$gdnoLists=array('BA5247-010');
$gdOutItems = []; //保存查询结果并即将存为文件的数组

$i=1;
$j=1;

foreach ($gdnoLists as $gdno) {
    $listItem = $query->queryList($gdno);

    if (intval($listItem->total) < 1) {
        print_r("{$gdno}未查询到资料!" . PHP_EOL);
        // return;
    }
    print_r("正在查找{$gdno}的条码信息....".PHP_EOL);

    $row = $listItem->rows[0];
    $barItems = $query->getBarCode($row->itemNo, $row->code, $row->colorName);
    foreach ($barItems->rows as $bar) {
        $gdOutItems[] = array(
            $row->code."-".$bar->sizeNo,
            $row->brandName,
            $row->code,
            $row->itemNo,
            $row->name,
            $row->fullName,
            $row->colorName,
            $row->genderName,
            $row->yearsName,
            $row->saleDate,
            $row->categoryName,
            $row->purchaseSeasonName,
            $bar->sizeKind,
            $bar->sizeNo,
            $bar->barcode,
            $row->tagPrice,
        );
        $query->getImgFile($gdno,$bar->barcode,$j);
        $i++;
        if($i==50){
            $i=1;
            $j++;
        }
    }
   
}

$gdnoFile->gdOutFile("gd2.xls",$gdOutItems);
$gdnoFile->gdOutFile2("gd3.xls",$gdOutItems);