<?php
require __DIR__ . '/vendor/autoload.php';
include 'dbconn.php';

$feed_url = "xmlFile.xml";
$object = new DOMDocument();
$object->load($feed_url);
$content = $object->getElementsByTagName("cd");

$result = $collection->drop();
    
foreach($content as $row)
{

     $result = $collection->insertOne( [ 'title' => $row->getElementsByTagName("title")->item(0)->nodeValue , 'artist' => $row->getElementsByTagName("artist")->item(0)->nodeValue, 'country' => $row->getElementsByTagName("country")->item(0)->nodeValue, 'company' => $row->getElementsByTagName("company")->item(0)->nodeValue, 'price' => $row->getElementsByTagName("price")->item(0)->nodeValue,'year' => $row->getElementsByTagName("year")->item(0)->nodeValue  ] );
 
}

echo "Successfully Added to MongoDB in localhost/27017 !!!";

?>