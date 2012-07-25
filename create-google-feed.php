<?
include_once('config.php');

// ENTER YOUR DATABASE CREDENTIALS 
    $db=DB_DATABASE;
	$link = mysql_connect('localhost', DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	if (! $link)
	die(mysql_error());
	mysql_select_db($db , $link)
	or die("Couldn't open $db: ".mysql_error());

	//$query_z_cats = "SELECT * FROM zen_categories_description";  	 
	$query_z_cats = 'SELECT a.categories_id, a.parent_id, b.categories_id, b.categories_name 
        FROM zen_categories a, zen_categories_description b
		WHERE a.parent_id=0 AND a.categories_id = b.categories_id';
	$cate_results = mysql_query($query_z_cats) or die(mysql_error());
	

?>
<!doctype html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Zen Cart Google Merchant Center Feed Creator</title>
	
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="form-processor.js" type="text/javascript"></script>

<style type="text/css">
body{font-family: Myriad, Myriad Pro, Arial;}
p{padding: 5px 0; color: #333; font-size: 14px;}
label{display: block; font-size: 16px;}
textarea{display: block; width:400px;}
</style>

</head>
<body>
<p>Be sure you've entered your Database Credentials in the CONFIG.PHP page. Everything else will be handled here.<br>
Defaults for Version 1: <br>
<strong>Price Type = Starting</strong><br>
<strong>Condition = New</strong><br>
<strong>Availability = In Stock</strong><br>
<strong>Age Group = Adult</strong><br>
<strong>Gender = Unisex</strong><br>
<strong>Color = Grey</strong><br>
</p>
<p>
<strong>Custom Variables:</strong><br>
&middot; Only get Products with Status = 1<br>
&middot; Only get 250 Characters from Description and Strip Quotes, Strip HTML, and strip middot b/c i used middot in my description and it was messing it up.<br>
&middot; Only show Root Categories below, Sub Categories would a lot redundant work. <span style="color:red;">Categories "if" script only goes up to 10. If you have more than 10 Root Categories it needs some tweakin.</span><br>
</p>
<p style="color:red;"><strong>URL Rewrite (SEO URLS is installed):</strong> Please do NOT have any - (dashes) or . (periods) in your Product Titles. The script is replacing spaces in the Product Title with - (dashes) and adding -p-ID#.html to the end.</p>
<form id="dataForm" action="">

<label>Enter a Short Description:</label>
<textarea name="desc_field"></textarea>
<hr>
<p>
<label>Match Your ROOT Categories with Googles Categories from the below link.<br>
**Enter the XML (<code>Cameras &amp; Optics &gt; Cameras</code>) in the box.</label>
<a href="http://support.google.com/merchants/bin/answer.py?hl=en&answer=160081" target="_blank">Categories Code Page</a> 
 | 
<a href="http://support.google.com/merchants/bin/answer.py?hl=en&answer=188494#US" target="_blank">Product Feed Specs</a> 
</p>

<table>
<?
        while($row = mysql_fetch_array($cate_results, MYSQL_ASSOC)) {
				
		echo "<tr><td><strong>" . $row['categories_name'] . "</strong></td>";
		echo "<td><input name=\"category" . $row['categories_id'] . "\" type=\"text\"></td>";
		echo "</tr>";
		}
		?>
</table>

<hr>
<button type="submit">CREATE FEED</button>
</form>

<div id="response" style="background:#ddd; color:#333; margin: 5px; padding: 0; width: 300px;"></div>
</body>
</html>