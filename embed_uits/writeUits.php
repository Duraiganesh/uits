<?php
/*
* TO write the UITS information to the MP3 PRIV tags
*/
// include getID3() library (can be in a different directory if full path is specified)
require_once('getid3/getid3.php');

// Initialize getID3 engine
$getID3 = new getID3;
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'write.id3v2.php', __FILE__, true); 
$TaggingFormat = 'UTF-8';    
$tagwriter = new getid3_write_id3v2();

//Set the path of file
$filename = 'mp3/sample.mp3';
$tagwriter->filename = $filename;
$tagwriter->tagformats     = array('id3v1', 'id3v2.3');

// set various options (optional)
$tagwriter->overwrite_tags = true;
$tagwriter->tag_encoding   = $TaggingFormat;
$tagwriter->remove_other_tags = true;

// 8 digit random value for the nonce input
$nonce = base64_encode(rand(1000,10000));

//Name of the distributor
$distributor = 'RGEN';

//purchase time
$time = '2013-10-30T13:15:04Z';

//Valid ProductID Types currently include "UPC" or "GRID"
$productID_type='UPC';

//true or false based on the purchase of tracks from the album	
$purchase_completed = 'false';
$productID = '00602517758056';

$assetID = 'ES1700800499';
$assetID_type = 'ISRC';

$transactionID = '39220345237';
$transactionID_type = '1';

$userID_version = '1';
$userID = 'A74GHY8976547B';

//Filter the media tags start
$ThisFileInfo = $getID3->analyze($filename);
getid3_lib::CopyTagsToComments($ThisFileInfo);
$filterOutKeys = array('GETID3_VERSION','filesize','filename','filepath','filenamepath','avdataoffset','avdataend','fileformat','warning','tags','encoding','id3v2','id3v1','tags_html','comments','comments_html');
$filteredArr = array_diff_key($ThisFileInfo, array_flip( $filterOutKeys ) );
$media_string = serialize($filteredArr['audio']);
$media_hash = hash('sha256',$media_string);
$media_hash_algorithm = 'SHA256';
//Filter the media tags end
	
$url_type = 'WPUB';
$url = 'http://www.sonymusic.com/';
$parentalAdvisory = 'explicit';
$copyright = 'allrightsreserved';

//Generate xml page start
require('createUitsXml.php');
//Generate xml page start

$TagData['PRIV'][0]['ownerid'] = 'http://www.sample.com';
//Embedded the UITS metadata	  
$TagData['PRIV'][0]['data'] = $data_uits;
      
$tagwriter->tag_data = $TagData;

// write tags
if($tagwriter->WriteID3v2()) {

  echo 'Successfully wrote tags<BR>';
  if(!empty($tagwriter->warnings)) {
    echo 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
  }
} else {
  echo 'Failed to write tags!<br>'.implode('<br><br>', $tagwriter->errors);
}
?>