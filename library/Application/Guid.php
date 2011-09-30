<?php
    
class Application_Guid
{
    /**
     * Generate a GUID
     *
     * @return string
     */
    public static function generate()
    {
        $cpuname     = getenv('COMPUTERNAME');
        $address     = isset($_SERVER['SERVER_ADDR']) ? @$_SERVER['SERVER_ADDR'] : uniqid(hash("md5", time()), true) . time();
        $address     = (trim($cpuname) == '' ? crypt(uniqid(rand(), true)) : $cpuname) . '/' . $address;
        $milisecs        = microtime();
        $randomLong  = (rand(0, 1))? '-':'';
        $randomLong .= rand(1000, 9999).rand(1000, 9999).rand(1000, 9999).rand(100, 999).rand(100, 999);
        
        $string = $address . ':' . $milisecs . ':' . $randomLong;
        $hashString = strtoupper(md5($string));
        
        return substr($hashString, 0, 8).'-'.substr($hashString, 8, 4).'-'.substr($hashString, 12, 4).'-'.substr($hashString, 16, 4).'-'.substr($hashString, 20);
    }
    
    
    /**
     * Verifies if a string is a valid Guid generated acording to this class
     *
     * @param string $guid Guid to be analyzed
     * @return boolean
     */
    public static function match($guid)
    {
        
        $match = preg_match("/^([A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12})$/", $guid);
        
        return $match;
    }
}