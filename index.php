<?php
	$ids        = '';
	$error      = '';
	$results    = array_fill(0,2,'');
	$results[1] = array_fill(0,1,'');
	$id         = array_fill(0,2,'');
	$id[1]      = array_fill(0,1,'');
	
	if (isset($_POST['extract'])) {
		$list = $_POST['listURL'];
		file_put_contents('history.log',date('Y-m-d H:i:s') . ',' . $list . " \n",FILE_APPEND);
		$test = preg_match('/https?:\/\/www.imdb.com\/list\//i',$list);

		if ($test == 0 || $test == false) {
			$error = "The URL entered must begin with '<a href='https://www.imdb.com/list/' target='_blank'>https://www.imdb.com/list/</a>'.<br /> (Example: <a href='https://www.imdb.com/list/ls071473494' target='_blank'>https://www.imdb.com/list/ls071473494/</a>)";
		} else {
			preg_match_all('/https?:\/\/www.imdb.com\/list\/(ls\d{9}).*/i',$list,$id);
			$site = file_get_contents('https://www.imdb.com/list/' . $id[1][0] . '/?start=1&view=compact&sort=listorian:asc&defaults=1&scb=0.4746716932859272');
			preg_match_all('/list-name.*>(.*)</mi',$site,$title);
			preg_match_all('/<a.*title\/(tt\d+).*ttls_li_tt/mi',$site,$results,PREG_PATTERN_ORDER);
			if (!$results[1][0]) {
				$error = "The URL entered does not contain a list of movies or is not in the correct format.<br /> (Example: <a href='https://www.imdb.com/list/ls071473494' target='_blank'>https://www.imdb.com/list/ls071473494/</a>)";
			}
		}
	}
?>

<!doctype html>
<html lang='en'>
    <head>
    	<meta charset='utf-8'>
    	<title>IMDB Movie List Extractor</title>
		<style>
			body {background-color: black;}
			hr {
				width: 66%;
				color: red;
				border-color: red;
				background-color: red;
			}
			div#container {
				border: 5px solid darkgray;
				border-radius: 25px;
				padding: 5px 5px 10px 10px;
				width: 50%;
				text-align: center;
				margin-left: auto;
				margin-right: auto;
				background: lightgray;
			}		
		</style>
		<script>
			/* Function called when Copy to Clipboard button clicked */
			function copyToClipboard(el) {
				var body = document.body, range, sel;
				if (document.createRange && window.getSelection) {
					range = document.createRange();
					sel = window.getSelection();
					sel.removeAllRanges();
					try {
						range.selectNodeContents(el);
						sel.addRange(range);
					} catch (e) {
						range.selectNode(el);
						sel.addRange(range);
					}
					document.execCommand('copy');
				} else if (body.createTextRange) {
					range = body.createTextRange();
					range.moveToElementText(el);
					range.select();
					range.execCommand('copy');
				}
			}
		</script>
    </head>
    <body>
    	<div id='container'>
    		<h1>IMDb Movie List Extractor</h1>
    		<p>This page will extract the list of IDs from any <a href='https://www.imdb.com/list/ls068082370/' target='_blank' title='Top 250 Movies'>IMDb movie list</a>.<br> 
    		    The list can then be copied and saved to a text file and imported into Couchpotato.<br>
    		    Use the <a href='https://couchpota.to/forum/viewtopic.php?f=17&t=4070' target='_blank'>Couchpotato Wanted List Backup and Restore script</a>.</p>
    		<form name='form1' method='post' action=''>
    			<label for='listURL'>List URL:</label> <input type='text' name='listURL'>
    			<input type='Submit' name='extract' value='Extract'>
				<?php if (!$error && isset($_POST['extract'])) {?>
					<button onclick='copyToClipboard(document.getElementById("results")); return false;'>Copy to Clipboard</button>
				<?php }?>
    		</form>
    		<?php 
    			if ($error) {print ('<hr /><br /><font color=red>ERROR: </font>' . $error . '<br>');}
    			else {
    			    if ($results[1][0]) {print ("<hr />\n<strong><a href='" . $list . "' target='_blank'>" . $title[1][0] . '</a> (' . count($results[1]) . ')</strong><br>');}
					print '<div id="results">';
    				foreach ($results[1] as $movie) {print ("\n" . $movie . '<br>');}
					print '</div>';
    			}
    		?>
    	</div>
    </body>
</html>
