<?php

// ------------------- Helper functions -------------------

// sort the call logs by the timestamp in ascending order
function sortByFirstElement($a, $b)
{
    return $a[0] <=> $b[0]; // Compare first elements of each sub-array
}

// sort the call logs by the item number in ascending order
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

// count the number of occurrences of a value in an array
function array_count_values_of($value, $array) {
    $counts = array_count_values($array);
    return $counts[$value];
}


// decrement all the elements in the array except the ones in the accessedItems array
function decrementAll($arr, $accessedItems)
{
    foreach ($arr as $key => $value) {
        if (!in_array($key, $accessedItems) && $value > 0) {
            $arr[$key] = $value - 1;
        }
    }

    return $arr;
}

function updatePriorities(&$priorities, &$cache, $item)
{
    if (isset($priorities[$item])) {
        $priorities[$item] += 2;
    } else {
        $cache[$item] += 2;
    }
}

// implement cacheContents function accoding to assignment
function cacheContents(array $callLogs): array
{
    // get the number of items to initialize the priorities array
    usort($callLogs, 'sortBySecondElement');
    $priorities = array_fill(1, $callLogs[count($callLogs) - 1][1], 0);

    // sort the call logs by the timestamp in ascending order
    usort($callLogs, 'sortByFirstElement');
    printSortedArray($callLogs);
    $accessedItems = [];
    $cache = [];
    $currentTime = 0;
    for ($i = 0; $i < count($callLogs); $i++) {
        // if the current timestamp is greater than the current time, update the current time
        if ($callLogs[$i][0] > $currentTime) {

            $temp = $i;

            $currentTime = $callLogs[$i][0];

            // clear the accessed items array
            $accessedItems = [];


            // push the accessed item to the accessed items array, doesn't matter if it is already there
            while ($i < count($callLogs) && $callLogs[$i][0] == $currentTime) {
                print "Current time: " . $currentTime . "\n";
                array_push($accessedItems, $callLogs[$i][1]);
                $i++;
            }

            $i = $temp;

            foreach ($accessedItems as $item) {
                updatePriorities($priorities, $cache, $item);
            }

            $priorities = decrementAll($priorities, $accessedItems);
            $cache = decrementAll($cache, $accessedItems);

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


$result = cacheContents($callLogs);

fwrite($fptr, implode("\n", $result) . "\n");

fclose($fptr);
