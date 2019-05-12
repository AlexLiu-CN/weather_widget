<?php
//获取ip
function get_client_ipv4(){
    $ip_check_url='http://whatismyip.akamai.com/';
    $client_ipv4 = file_get_contents($ip_check_url);
    echo $client_ipv4;
    return $client_ipv4;
}
get_client_ipv4();
?>