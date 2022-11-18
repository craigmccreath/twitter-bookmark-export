<?php

$file = json_decode(file_get_contents('twitter.com.har'), true);
@mkdir('Bookmarks');

$index = 1;
foreach ($file['log']['entries'] as $entry) {
    if (stristr($entry['request']['url'], '/Bookmark') === false) {
        continue;
    }

    if ($entry['response']['status'] !== 200) {
        continue;
    }

    $filename = 'Bookmarks' . str_pad($index, 4, '0', STR_PAD_LEFT) . ".json";
    file_put_contents('Bookmarks/' . $filename, $entry['response']['content']['text']);
    echo $filename . "\n";

    $index++;
}


