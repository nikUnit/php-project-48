<?php

namespace Differ\Differ;

use function cli\line;
use function cli\prompt;

function genDiff($pathToFile1, $pathToFile2)
{
    $workDirectory = getcwd();
    if (strpos($pathToFile1, $workDirectory) === false) {
        $pathToFile1 = getcwd().$pathToFile1;
    }
    if (strpos($pathToFile2, $workDirectory) === false) {
        $pathToFile2 = getcwd().$pathToFile2;
    }

    $file1 = json_decode(file_get_contents($pathToFile1), true);
    $file2 = json_decode(file_get_contents($pathToFile2), true);
    $resultMinus = addStringForArray(array_diff($file1, $file2), '  - ');
    $resultPlus = addStringForArray(array_diff($file2, $file1), '  + ');
    $resulIntersect = addStringForArray(array_intersect_assoc($file1, $file2), '    ');
    $result = array_merge($resultMinus, $resultPlus, $resulIntersect);
    uksort($result, function($a, $b)
        {
            $a = str_replace('  - ', '', $a);
            $b = str_replace('  - ', '', $b);
            $a = str_replace('  + ', '', $a);
            $b = str_replace('  + ', '', $b);
            return strcasecmp($a, $b);
        }
    );
    $resultString = "{\n";
    foreach ($result as $key => $value) {
        $resultString .= "{$key}: {$value}\n";
    }
    $resultString .= "}";
    return $resultString;
}

function addStringForArray(array $array, string $string) :array
{
    $newArr = [];
    foreach ($array as $key => $value) {
        $newArr[$string.$key] = $value;
    }
    return $newArr;
}

