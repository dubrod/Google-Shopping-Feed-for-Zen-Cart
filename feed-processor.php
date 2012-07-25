<?
include_once('config.php');

$desc = stripslashes($_POST[desc_field]);
$m=date("m")+1;
if($m<10){
$expiration = date("Y-0$m-j");
} else {
$expiration = date("Y-$m-j");
}

// GET CATEGORIES FROM BEFORE
$category1 = $_POST['category1'];
$category2 = $_POST[category2];
$category3 = $_POST[category3];
$category4 = $_POST['category4'];
$category5 = $_POST[category5];
$category6 = $_POST[category6];

// ENTER YOUR DATABASE CREDENTIALS 
    $db=DB_DATABASE;
	$link = mysql_connect('localhost', DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	if (! $link)
	die(mysql_error());
	mysql_select_db($db , $link)
	or die("Couldn't open $db: ".mysql_error());

// GET STORE NAME FROM DATABASE
	$query_z_config = "SELECT * FROM zen_configuration WHERE configuration_id = 1";  	 
	$config_results = mysql_query($query_z_config) or die(mysql_error());	
	$z_config = mysql_fetch_assoc($config_results);
	
	$store_name = $z_config['configuration_value'];
	
// GET PRODUCTS FROM DATABASE
	//$query_z_prods = "SELECT * FROM zen_products LIMIT 5"; // just testing? try this one 	 
	//$query_z_prods = "SELECT * FROM zen_products LEFT JOIN (zen_products_description,zen_manufacturers)
    //             ON (zen_products_description.products_id=zen_products.products_id AND zen_manufacturers.manufacturers_id=zen_products.manufacturers_id)";
    $query_z_prods = 'SELECT * 
        FROM zen_products a, zen_products_description b, zen_manufacturers c
		WHERE a.products_status=1 AND a.products_id = b.products_id AND c.manufacturers_id = a.manufacturers_id
		';             
	$prods_results = mysql_query($query_z_prods) or die(mysql_error());	
	$z_prods = mysql_fetch_assoc($prods_results);	
	
	
	mysql_close($link);




$file= fopen("zen-cart-google-feed.xml", "w");

 $_xml .="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
 $_xml .="<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\" xmlns:c=\"http://base.google.com/cns/1.0\">\r\n";

 $_xml .="<channel>\r\n";
 $_xml .="<copyright>" .$store_name. "</copyright>\r\n";
 $_xml .="<title>" .$store_name. "</title>\r\n"; 
 $_xml .="<pubDate>" .date(DATE_RFC822). "</pubDate>\r\n";
 $_xml .="<link>http://" .$_SERVER['SERVER_NAME']. "</link>\r\n";
 $_xml .="<description>".$desc."</description>\r\n";
 $_xml .="<generator>google</generator>\r\n"; 

while ($row = mysql_fetch_array($prods_results)) {

$urlslug1 = str_replace(".", "", $row['products_name']);
$urlslug2 = htmlspecialchars($urlslug1, ENT_QUOTES);
$urlslug = str_replace(" ", "-", $urlslug2);
$prods_desc_raw = strip_tags($row['products_description']);
$prods_replace = str_replace("&middot;", "-", $prods_desc_raw);
$prods_replace1 = str_replace("&reg;", "", $prods_replace);
$prods_replace2 = str_replace("&trade;", "", $prods_replace1);
$prods_replace3 = str_replace("&", "", $prods_replace2);
$prods_desc = substr($prods_replace3, 0, 250);
$trimmed_desc = trim($prods_desc);

$catVariable = $row['master_categories_id'];

$_xml .="<item>\r\n";
$_xml .="\t\t<link>http://" .$_SERVER['SERVER_NAME']. "/".$urlslug."-p-".$row['products_id'].".html</link>\r\n";
//c4a324gkd-comfortmaker-2-ton-14-seer-ac-p-79.html
$_xml .="\t\t<g:image_link>http://" .$_SERVER['SERVER_NAME']. "/images/".$row['products_image']."</g:image_link>\r\n";
$_xml .="\t\t<title>".$row['products_name']."</title>\r\n";
$_xml .="\t\t<description>".$trimmed_desc."</description>\r\n";
$_xml .="\t\t<g:id>".$row['products_id']."</g:id>\r\n";
$_xml .="\t\t<g:mpn>".$row['products_model']."</g:mpn>\r\n";
$_xml .="\t\t<g:brand>".$row['manufacturers_name']."</g:brand>\r\n";
$_xml .="\t\t<g:price>".$row['products_price']."</g:price>\r\n";
$_xml .="\t\t<g:price_type>starting</g:price_type>\r\n";
$_xml .="\t\t<g:condition>New</g:condition>\r\n";
$_xml .="\t\t<g:availability>In Stock</g:availability>\r\n";
$_xml .="\t\t<g:age_group>Adult</g:age_group>\r\n";
$_xml .="\t\t<g:gender>Unisex</g:gender>\r\n";
$_xml .="\t\t<g:color>Grey</g:color>\r\n";

$_xml .="\t\t<g:product_type>Home &amp; Garden</g:product_type>\r\n";
$_xml .="\t\t<g:google_product_category>Home &amp; Garden &gt; Household Appliances &gt; Climate Control &gt; Air Conditioners</g:google_product_category>\r\n";
//if($catVariable==4){$_xml .="\t\t<g:google_product_category>".$category4."</g:google_product_category>\r\n";}



$_xml .="\t\t<g:expiration_date>".$expiration."</g:expiration_date>\r\n";

$_xml .="</item>\r\n";
}

 $_xml .="</channel>"; 
 $_xml .="</rss>";

 fwrite($file, $_xml);

 fclose($file);



//Print done mesage
print "Done. <a href=\"zen-cart-google-feed.xml\">View Your Feed.</a>";

?>
