<?php
namespace src;

use SimpleExcel\SimpleExcel;
use \PHPExcel;

class gdno
{
    public function gdnoImport($filename)
    {
        $excel = new SimpleExcel('CSV');
        $excel->parser->loadFile($filename);
        $excel->convertTo("JSON");
        return $excel->parser->getColumn(1);
    }

    public function gdOutFile($filename, $data)
    {
        $excel = new SimpleExcel('CSV');
        
        print_r("准备写入{$filename}....".PHP_EOL);
        $excel->writer->setData($data);
        
        $excel->writer->saveFile($filename,$filename);
        print_r("写入{$filename}完成!".PHP_EOL);
    }

    public function gdOutFile2($filename,$data){
        $newData=array();
        foreach($data as $row){
            $newData[]=array(
                $row[2],
                \mb_substr($row[7],0,1).$row[10],
                $row[14],
                "",
                0,
                $row[15]*0.6,
                $row[15],
                $row[15],
                $row[15],
                "是",
                "是",
                10,
                1,
                "无",
                "",
                "",
                $row[6],
                $row[13],
                "启用",
                "{$row[8]} {$row[9]} {$row[7]} {$row[11]} {$row[6]} {$row[4]}"
            );
        }
        $excel = new SimpleExcel('CSV');
        //var_dump($newData);
        print_r("准备写入{$filename}....".PHP_EOL);
        $excel->writer->setData($newData);
        
        $excel->writer->saveFile($filename,$filename);
        print_r("写入{$filename}完成!".PHP_EOL);
    }
}
