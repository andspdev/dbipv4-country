<?php

function ip2longv4($ip) {
    return sprintf('%u', ip2long($ip));
}

function get_ip_country($ip_address) 
{
    global $pdo;

    $ip_long = ip2longv4($ip_address);
    $sql = "SELECT 
        code as kode, 
        name_country as negara
    FROM ip_country
    WHERE $ip_long 
    BETWEEN start_ip_num AND end_ip_num";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $country_code = $stmt->fetch(PDO::FETCH_OBJ);
    
    return $stmt->rowCount() > 0 ? $country_code : null;
}

function isIPLocalhost($ip) 
{
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }

    if ($ip === '127.0.0.1' || $ip === '::1')
        return true;

    $serverIpv4 = gethostbyname(gethostname());
    $serverIpv6 = gethostbynamel(gethostname());

    if (is_array($serverIpv6))
        $serverIpv6 = array_shift($serverIpv6);

    return $ip === $serverIpv4 || $ip === $serverIpv6;
}

function isValidIPv4($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}