<?php
function redirect($url){
 header('Refresh: 0.3; url=' . $url); 
}