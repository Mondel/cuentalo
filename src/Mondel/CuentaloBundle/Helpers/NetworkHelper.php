<?php

namespace Mondel\CuentaloBundle\Helpers;

class NetworkHelper
{
    
    /**
     * Devuelve el nombre del pais, segun la ip.
     * 
     * @param string $ip Ip de un usuario
     * @return string NombrePais calculado para la ip original
     */
    public static function getCountryNameByIp($ip)
    {
        $raw_data = file_get_contents('http://api.ipinfodb.com/v3/ip-country/?key=676a0b90542784ed97deb64a8870852363a5829d4cdaeec8e912f1aab6167eaa&ip='.$ip.'&format=json');
        $value = json_decode($raw_data);

        if ($value->statusCode == 'OK') {
            return $value->countryName;
        }
        
        return '';
    }
    
}