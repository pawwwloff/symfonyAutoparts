<?php


namespace App\Service;


use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;

class ReadCsvService
{

    public static function readTheFile($path, $count = 5, $skip = 0) {
        $handle = fopen($path, "r");
        $i = 0;
        while(!feof($handle) && $i<$count+$skip) {
            $i++;
            $line = trim(fgets($handle));
            if($i<=$skip) continue;
            yield $line;
        }

        fclose($handle);
    }

    public static function getLines($file, $count = 5, $skip = 0){
        $iterator = self::readTheFile($file, $count, $skip);
        $lines = [];
        $row = 1;
        foreach ($iterator as $iteration) {
            $line = (str_getcsv($iteration, ';'));
            if($row<count($line)){
                $row = count($line);
            }
            $lines[] = $line;
        }
        return ['lines'=>$lines, 'row'=>$row-1];
    }
}