<?php

$txt = $_REQUEST['xmlDoc'];

$myfile = fopen("xmlFile.xml", "w") or die("Unable to open file!");
fwrite($myfile, $txt);
fclose($myfile);

echo "Server XML Updated";
?>