<?php
namespace Data;

class Data
{

    private static $domain = '@mailinator.com';
    public static $scenario = array();
    public static $email;

    /**
     * Set scenario data
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function setData($key, $value)
    {
        return self::$scenario[$key] = $value;
    }

    /**
     * Get scenario data
     * @param $key
     * @return mixed|null
     */
    public static function getData($key)
    {
        if (array_key_exists($key, self::$scenario)){
            return self::$scenario[$key];
        }

        return null;
    }

    /**
     * Generates random email address
     * @return string
     */
    public static function generateRandomEmail(){
        self::$email = "t" . time() . self::$domain;
        return self::$email;
    }

    /**
     * @param null $length
     * @return string
     */
    public static function generateRandomString($length = null){
        if($length === null){ $length = 6; }
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($chars),0, $length);
    }


    public static function generateNews() {
        return include "news.php";
    }

    public static function getSearchTerm($search_term) {
        return $search_term;
    }

    public static function getAdminCredentials() {
        return ['user' => 'admin', 'password' => 'nublue2test'];
//        return ['user' => 'demo', 'password' => 'demo123'];
    }

}