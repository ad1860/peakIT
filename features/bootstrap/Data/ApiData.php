<?php
namespace Data;


class ApiData
{

    public static $account = [
        "customer" => [
            "email" => null,
            "firstname" => "test",
            "lastname" => null,
            "group_id" => 0,
            "store_id" => 1,
            "website_id" => 1
        ],
        "password" => "qA123123"
    ];

    public static function generateNewAccountDetails() {
        self::$account['customer']['lastname'] = Data::generateRandomString();
        self::$account['customer']['email'] = Data::generateRandomEmail();
        return self::$account;
    }
}