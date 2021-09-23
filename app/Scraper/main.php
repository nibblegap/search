<?php
	require_once "support/web_browser.php";
	require_once "support/tag_filter.php";


    $title_selector = 'div[class="g"] div[class="yuRUbf"] h3';
    $description_selector = 'div[class="g"] div.IsZvec';
    $url_selector = $title_selector;

	// Retrieve the standard HTML parsing array for later use.
	$htmloptions = TagFilter::GetHTMLOptions();

	// Retrieve a URL (emulating Firefox by default).
	$url = "http://google.com/search?q=".$_GET['q'];
	$web = new WebBrowser();
	$result = $web->Process($url);

    $results = array(
        'results' => []
    );
    $titles = array();

	// Check for connectivity and response errors.
	if (!$result["success"])
	{
		echo "Error retrieving URL.  " . $result["error"] . "\n";
		exit();
	}

	if ($result["response"]["code"] != 200)
	{
		echo "Error retrieving URL.  Server returned:  " . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
		exit();
	}

	// Get the final URL after redirects.
	$baseurl = $result["url"];

	// Use TagFilter to parse the content.
	$html = TagFilter::Explode($result["body"], $htmloptions);

	// Retrieve a pointer object to the root node.
	$root = $html->Get();

	// Find all anchor tags inside a div with a specific class.
	// A useful CSS selector cheat sheet:  https://gist.github.com/magicznyleszek/809a69dd05e1d5f12d01
	$_titles = $root->Find($title_selector);
	foreach ($_titles as $title)
	{
        if ($title->style != '-webkit-line-clamp:2') {
            array_push($titles, $title->GetPlainText());
        }
	}

    print_r($titles);
?>
