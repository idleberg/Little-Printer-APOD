<?php
	// Set headers
	$date = date(z);
	$md5 = md5($date);
	header("ETag: ".$md5);
	header("Content-Type: text/html; charset=utf-8");

	// Get link for latest post
	$rss = file_get_contents('http://apod.nasa.gov/apod.rss');
	$rss = new SimpleXMLElement($rss);

	// Get HTML for latest post
	$html = file_get_contents($rss->channel->item[0]->link);

	$doc = new DOMDocument();
	$doc->strictErrorChecking = false;
	$doc->recover=true;
	@$doc->loadHTML("<html><body>".$html."</body></html>");

	// Get first image
	$img = $doc->getElementsByTagName('img')->item(0);

	if ($img == NULL) {
		$error = "Error retrieving image, please try again later";
	} else {	
		// Get image title
		$title = $doc->getElementsByTagName('b')->item(0)->nodeValue;
		$title = trim($title);

		// Get credits
		$credit = $doc->getElementsByTagName('center')->item(1)->nodeValue;
		$credit = strip_tags($credit);
		$credit = str_replace($title , NULL, $credit); 
		$credit = str_replace("Image Credit &\nCopyright:", NULL, $credit);
		$credit = trim($credit);
	}
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Astronomy Picture of the Day</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=364">
	<!-- BERG Cloud doesn't allow the usage of relative URLs, you might have to fix the next line to work for you (http://remote.bergcloud.com/developers/faq/) -->
	<link rel="stylesheet" href="http://<?php echo $_SERVER['HTTP_HOST'] ?>/style.css" media="screen" type="text/css" />
</head>
<body>
	<div id="main">
		<h1 class="uppercase">Astronomy Picture of the Day</h1>
		
		<?php if (isset($error)) {
			echo "<p class=\"uppercase credit\">$error</p>";
		} else { ?>
		  	<img class="dither" src="http://apod.nasa.gov/<?php echo $img->attributes->getNamedItem("src")->value; ?>" />
			<h2>&#8220;<?php echo $title ?>&#8221;</h2>
			<p class="uppercase credit">&copy; <? echo $credit ?></p>
			<p class="credit"><a href="http://apod.nasa.gov">apod.nasa.gov</a></p>
		<?php } ?>
	</div>
</body>