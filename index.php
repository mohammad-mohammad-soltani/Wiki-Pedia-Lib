<?php
require "Wiki.php";


// Using the class
$wiki = new Wiki("fa");
file_put_contents("test.md" , $wiki->search("ویکی پدیا"));
