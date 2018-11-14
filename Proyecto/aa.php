<?php
    $name = "camilo";
    $url = "https://daw-ad18-ejuarezp.c9users.io/lab25_servicio/public/index.php/$name"; //Route to the REST web service
    $c = curl_init($url);
    $response = curl_exec($c);
    curl_close($c);
    //var_dump($response); 
?>