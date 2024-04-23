<?php
// implement cacheContents function accoding to assignment
function cacheContents(array $callLogs):array
{
    return [];
}

$outputPath = getenv("OUTPUT_PATH") && getenv("OUTPUT_PATH") !=='' ? getenv("OUTPUT_PATH") : "output.txt";
$fptr = fopen($outputPath, "w");

$callLogs_rows = intval(trim(fgets(STDIN)));
$callLogs_columns = intval(trim(fgets(STDIN)));

$callLogs = array();

for ($i = 0; $i < $callLogs_rows; $i++) {
    $callLogs_temp = rtrim(fgets(STDIN));

    $callLogs[] = array_map('intval', preg_split('/ /', $callLogs_temp, -1, PREG_SPLIT_NO_EMPTY));
}

// sort the call logs by the timestamp in ascending order
function sortByFirstElement($a, $b) {
    return $a[0] <=> $b[0]; // Compare first elements of each sub-array
}

usort($callLogs, 'sortByFirstElement');

// print the sorted call logs
foreach ($callLogs as $subArray) {
    fwrite(STDOUT, $subArray[0] . ": " . $subArray[1] . PHP_EOL);
}

$result = cacheContents($callLogs);

fwrite($fptr, implode("\n", $result) . "\n");

fclose($fptr);
