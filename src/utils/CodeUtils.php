<?php

declare(strict_types=1);

namespace phuongaz\giftcode\utils;

use phuongaz\giftcode\Loader;
use pocketmine\utils\Config;

class CodeUtils
{

    public static function getConfig() :Config
    {
        return Loader::getInstance()->getCodesConfig();
    }

    public static function exists(string $code): bool
    {
        return self::getConfig()->exists($code);
    }

    public static function getItems(string $code): ?array
    {
        if(self::exists($code)){
            return null;
        }
        $items = json_decode(self::getConfig()->get($code)["items"], true);
        return array_map(function($item){
            return Utils::unserializeItem($item);
        }, $items);
    }

    public static function getCommands(string $code): ?array
    {
        if(!self::exists($code)){
            return null;
        }
        return json_decode(self::getConfig()->get($code)["commands"], true);
    }

    public static function getCodes() :array
    {
        return array_keys(self::getConfig()->getAll());
    }
}