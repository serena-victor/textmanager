<?php

$fetchurl = '';
$text = '';
if (isset($_POST['fetch'])) {
    $fetchurl = $_POST['fetch'];
    $content = file_get_contents($fetchurl);
    if (gzdecode($content) != false) {
        $content = gzdecode($content);
    }

    $text = htmlentities($content);
}

$keywords = '';
$display = false;
if (isset($_POST['keywords']) && trim($_POST['keywords']) != false) {
    $display = true;
    $keywords = $_POST['keywords'];
    $keywordArray = preg_split('/\s+/', $keywords);

    $occurrences = [];
    foreach ($keywordArray as $keyword) {
        $occurrences[$keyword] = [];
    }

    $words = preg_split('/\s+/', $content);
    $counter = 1;
    foreach ($words as $key => $word) {
        if (trim($word) == false)
            continue;
        foreach ($keywordArray as $keyword)
            if (strpos($word, $keyword) !== false) {
                array_push($occurrences[$keyword], $keyword . $counter++);
            }
    }

    foreach ($occurrences as $key => $keyword) {
        $counter = 0;
        foreach ($keyword as $nb => $occurenceNumber) {
            $highlight = "<span id='$occurenceNumber' class='bg-warning'>$key</span>";
            $text = preg_replace_nth('/' . $key . '/', $highlight, $text, $nb + (++$counter));
        }
    }


}

include('textmanager.html');


function preg_replace_nth($pattern, $replacement, $subject, $nth = 0)
{
    return preg_replace_callback($pattern,
        function ($found) use (&$pattern, &$replacement, &$nth) {
            $nth--;
            if ($nth == 0) return preg_replace($pattern, $replacement, reset($found));
            return reset($found);
        }, $subject, $nth);
}