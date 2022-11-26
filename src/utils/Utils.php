<?php

declare(strict_types=1);

namespace phuongaz\giftcode\utils;

use phuongaz\giftcode\Loader;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;

class Utils
{
    public static function parseTimeFormat(string $duration): ?int
    {
        $parts = str_split($duration);
        $time_units = ['y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second']; //Array of replacement
        $time = '';
        $i = -1;
        foreach ($parts as $part) {
            ++$i;
            if (!isset($time_units[$part])) {
                if (ctype_alpha($part)) return null; //Ensure only valid characters should pass
                continue;
            }
            $unit = $time_units[$part];
            $n = implode('', array_slice($parts, 0, $i));
            $time .= "$n $unit "; //Join number and unit
            array_splice($parts, 0, $i + 1);
            $i = -1;
        }
        $time = trim($time);
        $epoch = strtotime($time, 0);
        if ($epoch === false) return null;
        return $epoch;
    }

    public static function parseSecondToHuman($seconds): ?string
    {
        $dt1 = new \DateTime("@0");
        $dt2 = new \DateTime("@$seconds");
        $diff = $dt1->diff($dt2);
        if ($diff === false) return null;
        $str = [];
        if ($diff->y > 0) $str[] = $diff->y . ' year(s)';
        if ($diff->m > 0) $str[] = $diff->m . ' month(s)';
        if ($diff->d > 0) $str[] = $diff->d . ' day(s)';
        if ($diff->h > 0) $str[] = $diff->h . ' hour(s)';
        if ($diff->i > 0) $str[] = $diff->i . ' minute(s)';
        if ($diff->s > 0) $str[] = $diff->s . ' second(s)';
        if (count($str) > 0) {
            $str = implode(', ', $str);
        } else {
            $str = $diff->s . ' second';
        }
        return $str;
    }

    public static function getTimeLeft(string $code): ?string
    {
        $date = date("Y-m-d H:i:s");
        $temp = Loader::getInstance()->getCodesConfig()->getNested($code . ".temp");
        $enddate = date("Y-m-d H:i:s", strtotime($date) + $temp);
        if (strtotime($enddate) < time()) {
            return null;
        }
        $datetime1 = date_create($date);
        $datetime2 = date_create($enddate);
        $interval = date_diff($datetime1, $datetime2);
        $min = $interval->format('%i');
        $sec = $interval->format('%s');
        $hour = $interval->format('%h');
        $mon = $interval->format('%m');
        $day = $interval->format('%d');
        $year = $interval->format('%y');
        if ($interval->format('%i%h%d%m%y') == "00000") {
            return $sec . " Seconds";
        } else if ($interval->format('%h%d%m%y') == "0000") {
            return $min . " Minutes";
        } else if ($interval->format('%d%m%y') == "000") {
            return $hour . " Hours";
        } else if ($interval->format('%m%y') == "00") {
            return $day . " Days";
        } else if ($interval->format('%y') == "0") {
            return $mon . " Months";
        } else {
            return $year . " Years";
        }
    }

    public static function randomString(string $startWith = "", $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $startWith . $randomString;
    }

    public static function serializeItem(Item $item) :string
    {
        return base64_encode(serialize($item));
    }

    public static function unserializeItem(string $item) :Item
    {
        return unserialize(base64_decode($item));
    }

    public static function sendToast(Player $player, string $title, string $message) :void
    {
        $packet = ToastRequestPacket::create($title, $message);
        $player->getNetworkSession()->sendDataPacket($packet);
    }

}