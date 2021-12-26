<?php
require_once "support/web_browser.php";
require_once "support/tag_filter.php";

function parseResults()
{
    $title_selector = 'div[class="g"] div[class="yuRUbf"] h3';
    $description_selector = 'div[class="g"] div.IsZvec';
    $url_selector = $title_selector;

    // Retrieve the standard HTML parsing array for later use.
    $htmloptions = TagFilter::GetHTMLOptions();

    // Retrieve a URL (emulating Firefox by default).
    $url = "http://google.com/search?q=" . urlencode($_GET["q"]) . "&ie=UTF-8";
    $web = new WebBrowser();
    $result = $web->Process($url);

    $final_data = [
        "results" => [],
    ];
    $titles = [];
    $descriptions = [];
    $urls = [];

    // Check for connectivity and response errors.
    if (!$result["success"]) {
        echo "Error retrieving URL.  " . $result["error"] . "\n";
        exit();
    }

    if ($result["response"]["code"] != 200) {
        echo "Error retrieving URL.  Server returned:  " .
            $result["response"]["code"] .
            " " .
            $result["response"]["meaning"] .
            "\n";
        exit();
    }

    // Get the final URL after redirects.
    $baseurl = $result["url"];

    // Use TagFilter to parse the content.
    $html = TagFilter::Explode($result["body"], $htmloptions);

    // Retrieve a pointer object to the root node.
    $root = $html->Get();

    // find all titles
    $_titles = $root->Find($title_selector);
    foreach ($_titles as $title) {
        if ($title->style != "-webkit-line-clamp:2") {
            array_push($titles, $title->GetPlainText());
        }
    }

    // find all descriptions
    $_descriptions = $root->Find($description_selector);
    foreach ($_descriptions as $desc) {
        if (in_array("w1C3Le", $desc->Parent()->class) == false) {
            array_push($descriptions, $desc->GetInnerHTML());
        }
    }

    // find all urls
    $_urls = $root->Find($url_selector);
    foreach ($_urls as $url) {
        $url = $url->Parent();
        array_push($urls, $url->href);
    }

    // corecting fuzzy data
    $titles_len = sizeof($titles);
    $descriptions_len = sizeof($descriptions);
    $urls_len = sizeof($urls);

    if ($titles_len < $urls_len and $titles_len < $descriptions_len) {
        array_shift($urls);
    } elseif ($urls_len > $titles_len) {
        array_shift($urls);
    }

    $urls_len = sizeof($urls);
    $inacurate =
        $descriptions_len > sizeof(array_slice($urls, 1)) ? false : true;

    $i = 0;
    foreach ($urls as $item) {
        if (
            str_contains($item, "youtube.com") and
            $inacurate and
            $urls_len > 1
        ) {
            array_splice($urls, $i, 1);
            array_splice($titles, $i, 1);
            $i--;
        }

        $i++;
    }

    $i = 0;
    foreach ($titles as $title) {
        array_push($final_data["results"], [
            "title" => $title,
            "description" => str_replace(
                "</em>",
                "</b>",
                str_replace("<em>", "<b>", $descriptions[$i])
            ),
            "url" => $urls[$i],
        ]);

        $i++;
    }

    return $final_data;
}
?>
