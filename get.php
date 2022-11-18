<?php

// Based on https://gist.github.com/divyajyotiuk/9fb29c046e1dfcc8d5683684d7068efe

$count = 0;
$all_bookmarks = [];
$md_file = fopen('bookmarks.md', 'w');

foreach(glob('Bookmarks/*') as $file) {
    $all_bookmarks[] = json_decode(file_get_contents($file), true);
}

function constructUrl($tweet_id, $username) {
    return "https://twitter.com/{$username}/status/{$tweet_id}";
}

function formatText($text) {
    $text = str_replace("\n-", " ", $text);
    $text = str_replace("\n", " ", $text);
    $text = html_entity_decode($text);
    return $text;
}

// https://stackoverflow.com/a/2287029
function getNestedVar(&$context, $name) {
    $pieces = explode('.', $name);
    foreach ($pieces as $piece) {
        if (!is_array($context) || !array_key_exists($piece, $context)) {
            // error occurred
            return null;
        }
        $context = &$context[$piece];
    }
    return $context;
}

foreach ($all_bookmarks as $data) {
    $instructions_list = getNestedVar($data, 'data.bookmark_timeline_v2.timeline.instructions');

    if (empty($instructions_list)) {
        // In testing, my wife's account had _v2 but I was still on v1. However, the json we need is the same.
        $instructions_list = getNestedVar($data, 'data.bookmark_timeline.timeline.instructions');
    }

    if (empty($instructions_list)) {
        continue;
    }

    $tweet_entries_list = getNestedVar($instructions_list[0], 'entries');
    foreach ($tweet_entries_list as $tweet_entry) {
        $result = getNestedVar($tweet_entry, 'content.itemContent.tweet_results.result');
        $username = getNestedVar($result, 'core.user_results.result.legacy.screen_name');
        $text = getNestedVar($result, 'legacy.full_text');
        $created_at = getNestedVar($result, 'legacy.created_at');
        $tweet_id = getNestedVar($result, 'rest_id');

        if (empty($tweet_id) || empty($username) || empty($text)) {
            continue;
        }

        // Convert t.co links to full links in text
        $urls = getNestedVar($result, 'core.user_results.result.legacy.entities.url.urls');
        if (!empty($urls)) {
            foreach ($urls as $full_url) {
                $text = str_replace($full_url['url'], '', $text);
                $text .= ' ' . $full_url['expanded_url'];
            }
        }

        $text = formatText($text);
        $url = constructUrl($tweet_id, $username);
        $markdown = "\n- {$text}\n\t - {$url} ({$created_at}";
        fputs($md_file, $markdown);
        $count++;
    }
}

echo "Completed. {$count} bookmarks saved.\n";