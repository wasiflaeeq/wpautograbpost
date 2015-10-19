<?php
error_reporting(0);
/*
Plugin Name: AutoPoster
Plugin URI: http://www.tealwings.com
Description: :p
Version: 0.1
Author: The Tealwings Team
Author URI: http://www.tealwings.com

-----------------------------------------------------
*/

// for top1

$wp_upload_dir = wp_upload_dir();

$dir=$wp_upload_dir['basedir'];



function addthispost($title,$url)
{
$title=str_replace(" amp"," and ",$title);

if (!get_page_by_title($title,'OBJECT',"post")) :

    //addthispost($title,$url);


	 $name=$title;

$content=getcontent($url);


$pos=strrpos($content,"<br>");
if($pos>=20)
{
 $content=substr($content,0,$pos);
}

 $content=strip_tags($content);

$content=str_replace("]]&gt;","",$content);
$content=str_replace("&#13;","",$content);
$content=str_replace("]]>","",$content);

$pos=strpos($content,"Related posts");
if($pos>=20)
{
 $content=substr($content,0,$pos);
}






$content1=rewrite($content);

$my_post = array(
  'post_title'    => $title,
  'post_content'  => $content1,
  'post_status'   => 'draft',
  'post_author'   => 1,
  'post_category' => array(3,4)
);

// Insert the post into the database
$pid=wp_insert_post( $my_post );
//$url = "http://www.fashionlake.com/zara-shahjahan-latest-nice-eid-collection-2013-for-women/";

$parts = parse_url($url);

 		if ($parts['host'] == 'fashionhuntworld.blogspot.com' || $parts['host'] == 'www.fashionhuntworld.blogspot.com') 
 		{
 			$imageurls=getimagepages_fashionhuntworld($url);
 			saveimages($imageurls,$name,$imagepages,$pid);


 		}
 		else
 		{
  $imagepages=getimagepages($url);
$imageurls=getimageurls($imagepages);
saveimages($imageurls,$name,$imagepages,$pid);

}

  $my_post = array();
  $my_post['ID'] = $pid;
  $my_post['post_status'] = 'publish';
//$my_post->post_status = 'publish'; // use any post status
        wp_update_post( $my_post );
endif;



 }

function rewrite($text)
{
	$url = 'http://publisheer.com/api/';
$fields_string="";
$fields = array(
						'username' => "wasiflaeeq",
						'pass' => "test123",
						'text' => urlencode($text),
						'words_keep' => urlencode("collection"),
						'no_common' =>"1"
						
				);


//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//execute post
$result = curl_exec($ch);
//var_dump($result);
//close connection
curl_close($ch);
return $result;
}


function addthisattachment($pid,$filename,$postname,$n,$postname2)
{


//$pid=3196;
//$filename="mobilink.png";

  $wp_filetype = wp_check_filetype($filename, null );
  $wp_upload_dir = wp_upload_dir();

  //var_dump($wp_upload_dir);
   $attachment = array(
     'guid' => $wp_upload_dir['baseurl']."/" .$postname. '/' . basename( $filename ), 
     'post_mime_type' => $wp_filetype['type'],
     'post_title' => $postname2." ".$n,
     'post_content' => '',
     'post_status' => 'inherit'
  );
   $attach_id = wp_insert_attachment( $attachment, $filename, $pid );
 
   require_once(ABSPATH . 'wp-admin/includes/image.php');
   $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
   wp_update_attachment_metadata( $attach_id, $attach_data );
   set_post_thumbnail( $pid, $attach_id );
}
















function saveimages($imageurls,$postname,$imagepages,$pid)
{
	$n=0;
	
	$wp_upload_dir = wp_upload_dir();

$dir=$wp_upload_dir['basedir'];
	//echo $dir."/".$postname;
$postname2=$postname;
$postname=str_replace(" ","-",$postname);
$postname1=$postname;
	mkdir($dir."/".$postname);
	foreach($imageurls as $image)
	{
		$ref=$imagepages[$n];
		//echo $image;
		$n++;
		$name = $dir."/".$postname."/".$postname."-".$n.".jpg";
		//copy($image,"/test.jpg");
		//var_dump($ref);
		save_image($image,$name,$ref);
		addthisattachment($pid,$name,$postname1,$n,$postname2);
//die();

	}






}


function saveimages_fashionhuntworld($imageurls,$postname,$imagepages)
{
	$n=0;
	mkdir($postname);
	foreach($imageurls as $image)
	{
		$ref=$imagepages;
		//echo $image;
		$n++;
		$name = $postname."/".$postname." - ".$n.".jpg";
		//copy($image,"/test.jpg");
		//var_dump($ref);
		save_image($image,$name,$ref);
//die();

	}





}

function save_image($inPath,$fullpath,$ref) {
$ch = curl_init($inPath);
//curl_setopt($ch, CURLOPT_URL, $inPath);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
curl_setopt($ch, CURLOPT_REFERER, $ref);
 $rawdata = curl_exec($ch);
 //echo ";";
 //var_dump($html);
  //fwrite($fp,$inbuf);
  //fclose($fp);
 if(file_exists($fullpath)){
        unlink($fullpath);
    }
    $fp = fopen($fullpath,'x');
    fwrite($fp, $rawdata);
    fclose($fp);

    //echo $fullpath.",";
}

function getimageurls($pages)
{
	$images=array();
	foreach($pages as $page)
	{
		
		$file_string = file_get_contents($page);
		$dom = new DomDocument();

		@$dom->loadHTML($file_string);
		//var_dump($dom);

		$parts = parse_url($page);

 		if ($parts['host'] == 'fashionlake.com' || $parts['host'] == 'www.fashionlake.com') 
 		{
			$finder = new DomXPath($dom);
			$classname="attachment-800x800";
			$nodes = $finder->query("//*[contains(@class, '$classname')]");
			foreach ($nodes as $node)
	    	{
	     		 $img_url=$node->getAttribute('src');
	     		array_push($images, $img_url);
	     	}


		}
		elseif ($parts['host'] == 'style.pk' || $parts['host'] == 'www.style.pk')
		{
			$finder = new DomXPath($dom);
			$classname="entry";
			$nodes = $finder->query("//*[contains(@class, '$classname')]");
			foreach ($nodes as $node)
	    	{
	    		$nody=$node->getElementsByTagName('img');
	    		foreach ($nody as $node)
	    		{
	    			$node=$node;
	    		}
	     		$img_url=$node->getAttribute('src');
	     		//var_dump($img_url);
	     		//sleep(1);
	     		array_push($images, $img_url);
	     	}


		}


	}


return $images;

}


function getimagepages($url)
{
$file_string = file_get_contents($url);
//echo $file_string;
//echo $url;
//echo $file_string;

$dom = new DomDocument();

@$dom->loadHTML($file_string);


//var_dump($dom);


$finder = new DomXPath($dom);
$classname="gallery-icon";
$nodes = $finder->query("//*[contains(@class, '$classname')]");
$images=array();
foreach ($nodes as $node)
    {
     // $img_url=$node->getAttribute('src');

    	$nody=$node->getElementsByTagName('a');
    	//var_dump($nody);
    	foreach ($nody as $node)
    	{

    	$node=$node->getAttribute('href');
    	array_push($images, $node);
    	//var_dump($node);
    	}

	}

	return $images;

}


function getcontent($url)
{
$file_string = file_get_contents($url);
//echo $file_string;
//echo $url;
//echo $file_string;

$dom = new DomDocument();

@$dom->loadHTML($file_string);


//var_dump($dom);


$finder = new DomXPath($dom);
$classname="entry-content";
$nodes = $finder->query("//*[contains(@class, '$classname')]");
$content="-";
foreach ($nodes as $node)
    {
     
     $content=$dom->saveXML($node);

	}

	return $content;

}


function getimagepages_fashionhuntworld($url)
{
$file_string = file_get_contents($url);
//echo $file_string;
//echo $url;
//echo $file_string;

$dom = new DomDocument();

@$dom->loadHTML($file_string);


//var_dump($dom);


$finder = new DomXPath($dom);
$classname="separator";
$nodes = $finder->query("//*[contains(@class, '$classname')]");
$images=array();
foreach ($nodes as $node)
    {
     // $img_url=$node->getAttribute('src');

    	$nody=$node->getElementsByTagName('a');
    	//var_dump($nody);
    	foreach ($nody as $node)
    	{

    	$node=$node->getAttribute('href');
    	if (strpos($node,'.jpg') !== false || strpos($node,'.JPG') !== false)
    	array_push($images, $node);
    	//var_dump($node);
    	}

	}

	return $images;

}






function eap_admin_menu() {

        add_options_page("EAP", "EAP", 1, "EAP", "publisheer_admin");
        // add_menu_page('Publisheer', 'Publisheer', 8, __FILE__, 'publisheer_admin');
        // add_submenu_page(__FILE__, 'Article Spinner', 'Article Spinner', 8, 'publisheer-spinner', 'publisheer_spinner');
        // add_submenu_page(__FILE__, 'Add Content', 'AutoPoster', 8, 'publisheer-autopost', 'publisheer_autopost');
        // add_submenu_page(__FILE__, 'Quick Help', 'Quick Help', 8, 'publisheer-help', 'publisheer_help');
}









add_action("admin_menu", "eap_admin_menu");


 //add_action("admin_head", "testurls");



function clean($string) {
   $string = str_replace(" ", "-", $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = str_replace(" amp"," and",$string);

   return preg_replace('/-+/', ' ', $string); // Replaces multiple hyphens with single one.
}



function getposturls($url)
{
$file_string = file_get_contents($url);
//echo $file_string;
//echo $url;
//echo $file_string;

$dom = new DomDocument();

@$dom->loadHTML($file_string);


//var_dump($dom);


$finder = new DomXPath($dom);
$classname="entry-title";
$nodes = $finder->query("//*[contains(@class, '$classname')]");
$images=array();
foreach ($nodes as $node)
    {
     // $img_url=$node->getAttribute('src');

    	$nody=$node->getElementsByTagName('a');
    	//var_dump($nody);
    	foreach ($nody as $node)
    	{

    	$link=$node->getAttribute('href');
    	$title=strip_tags($dom->saveXML($node));
    	$title=clean($title);

    	array_push($images, array($link,$title));
    	//var_dump($node);
    	}

	}

	return $images;

}

//add_action( 'admin_init', 'prefix_do_this_hourly' );
 function testurls()
 {

 	$url="http://www.fashionlake.com/";
 	//$url="http://www.fashionlake.com/page/24/";
//  	$url="http://www.fashionhuntworld.blogspot.com/search?updated-max=2013-08-31T10%3A42%3A00-07%3A00&max-results=20";
 $urlss=getposturls($url);
// //var_dump($urlss);

 foreach($urlss as $url)
 {
 	$title=$url[1];
 	$url=$url[0];

 	if (!get_page_by_title($title,'OBJECT',"post")) :

     addthispost($title,$url);

 	endif;
 }

 }

add_action( 'wp', 'prefix_setup_schedule' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function prefix_setup_schedule() {
	if ( ! wp_next_scheduled( 'prefix_hourly_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'prefix_hourly_event');
	}
}


add_action( 'prefix_hourly_event', 'prefix_do_this_hourly' );
/**
 * On the scheduled action hook, run a function.
 */
function prefix_do_this_hourly() {

// $url="http://www.fashionlake.com/";
//  	//$url="http://www.fashionlake.com/page/12/";
// $urlss=getposturls($url);
// //var_dump($urlss);

// foreach($urlss as $url)
// {
// 	$title=$url[1];
// 	$url=$url[0];

// 	if (!get_page_by_title($title,'OBJECT',"post")) :

//     addthispost($title,$url);

// endif;

	


// }



$url="http://www.fashionhuntworld.blogspot.com/";
	//$url="http://fashionhuntworld.blogspot.com/search?updated-max=2013-04-28T01%3A06%3A00-07%3A00&max-results=4";
$urlss=getposturls($url);
//var_dump($urlss);

foreach($urlss as $url)
{
	$title=$url[1];
	$url=$url[0];

	if (!get_page_by_title($title,'OBJECT',"post")) :

   // addthispost($title,$url);

endif;
}

	// do something every hour
}









?>
