<?php
// implement cacheContents function accoding to assignment
function cacheContents(array $callLogs): array
{
    // get the number of items to initialize the priorities array
    usort($callLogs, 'sortBySecondElement');
    $priorities = array_fill(1, $callLogs[count($callLogs) - 1][1], 0);

    // sort the call logs by the timestamp in ascending order
    usort($callLogs, 'sortByFirstElement');
    $accessedItems = [];
    $cache = [];
    $currentTime = 0;
    for ($i = 0; $i < count($callLogs); $i++) {
        // if the current timestamp is greater than the current time, update the current time
        if ($callLogs[$i][0] > $currentTime) {

            $currentTime = $callLogs[$i][0];

            // clear the accessed items array
            $accessedItems = [];


            // push the accessed item to the accessed items array, doesn't matter if it is already there
            array_push($accessedItems, $callLogs[$i][1]);


            $priorities = decrementAll($priorities, $accessedItems);
            $cache = decrementAll($cache, $accessedItems);


        } else {
            // push the accessed item to the accessed items array, doesn't matter if it is already there
            array_push($accessedItems, $callLogs[$i][1]);

        }
        if (isset($cache[$callLogs[$i][1]])) {
            $cache[$callLogs[$i][1]] += 2 * array_count_values_of($callLogs[$i][1], $accessedItems);
        } else {
            $priorities[$callLogs[$i][1]] += 2 * array_count_values_of($callLogs[$i][1], $accessedItems);
        }

        // check all the priorities and move the items to the cache with priority higher than 5
        foreach ($priorities as $key => $value) {
            if ($value > 5) {
                $cache[$key] = $value;
                unset($priorities[$key]);
            }
        }

        // check the cache and move the items with priority less than 4 to the main memory
        foreach ($cache as $key => $value) {
            if ($value < 4) {
                $priorities[$key] = $value;
                unset($cache[$key]);
            }
        }

        print "Priorities: ";
        print_r($priorities);
        print "\n";
        print "Cache: ";
        print_r($cache);
        print_r("\n");
        print_r("-------------------\n");
    }

    //return the indices of nonzero elements in the cache
    $array_keys1 = array_keys(array_filter($cache));
    sort($array_keys1);
    return $array_keys1;
}

$outputPath = getenv("OUTPUT_PATH") && getenv("OUTPUT_PATH") !== '' ? getenv("OUTPUT_PATH") : "output.txt";
$fptr = fopen($outputPath, "w");

$callLogs_rows = intval(trim(fgets(STDIN)));
$callLogs_columns = intval(trim(fgets(STDIN)));

$callLogs = array();

for ($i = 0; $i < $callLogs_rows; $i++) {
    $callLogs_temp = rtrim(fgets(STDIN));

    $callLogs[] = array_map('intval', preg_split('/ /', $callLogs_temp, -1, PREG_SPLIT_NO_EMPTY));
}

// sort the call logs by the timestamp in ascending order
function sortByFirstElement($a, $b)
{
    return $a[0] <=> $b[0]; // Compare first elements of each sub-array
}

function sortBySecondElement($a, $b)
{
    return $a[1] <=> $b[1]; // Compare second elements of each sub-array
}

function printSortedArray($callLogs)
{
    foreach ($callLogs as $subArray) {
        fwrite(STDOUT, $subArray[0] . ": " . $subArray[1] . PHP_EOL);
    }
}

function array_count_values_of($value, $array) {
    $counts = array_count_values($array);
    return $counts[$value];
}

function decrementAll($arr, $accessedItems)
{
    foreach ($arr as $key => $value) {
        if (!in_array($key, $accessedItems) && $value > 0) {
            $arr[$key] = $value - 1;
        }
    }

    return $arr;
}


$result = cacheContents($callLogs);

fwrite($fptr, implode("\n", $result) . "\n");

fclose($fptr);
