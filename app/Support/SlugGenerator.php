<?php

namespace App\Support;

use Illuminate\Support\Str;

class SlugGenerator
{
    public static function make(?string $value, string $fallback = 'item'): string
    {
        $value = trim((string) $value);
        $fallback = trim($fallback) !== '' ? $fallback : 'item';

        $slugSource = preg_match('/[\x{0E00}-\x{0E7F}]/u', $value)
            ? self::romanizeThai($value)
            : $value;

        $slug = Str::slug($slugSource);

        if ($slug === '') {
            $slug = Str::slug(self::romanizeThai($value));
        }

        if ($slug === '') {
            $slug = Str::slug($fallback);
        }

        return $slug !== '' ? $slug : 'item';
    }

    public static function romanizeThai(string $value): string
    {
        $replacements = [
            'พระ' => 'phra',
            'วัด' => 'wat',
            'ธรรมะ' => 'dhamma',
            'ธรรม' => 'dhamma',
            'กรุงเทพ' => 'bangkok',
            'เชียงใหม่' => 'chiang-mai',
            'ภูเก็ต' => 'phuket',
            'อยุธยา' => 'ayutthaya',
            'สุโขทัย' => 'sukhothai',
        ];

        $value = strtr($value, $replacements);

        $characters = [
            'ก' => 'k', 'ข' => 'kh', 'ฃ' => 'kh', 'ค' => 'kh', 'ฅ' => 'kh', 'ฆ' => 'kh',
            'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch', 'ช' => 'ch', 'ซ' => 's', 'ฌ' => 'ch',
            'ญ' => 'y', 'ฎ' => 'd', 'ฏ' => 't', 'ฐ' => 'th', 'ฑ' => 'th', 'ฒ' => 'th',
            'ณ' => 'n', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th', 'ท' => 'th', 'ธ' => 'th',
            'น' => 'n', 'บ' => 'b', 'ป' => 'p', 'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph',
            'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm', 'ย' => 'y', 'ร' => 'r', 'ล' => 'l',
            'ว' => 'w', 'ศ' => 's', 'ษ' => 's', 'ส' => 's', 'ห' => 'h', 'ฬ' => 'l',
            'อ' => 'o', 'ฮ' => 'h',
            'ะ' => 'a', 'ั' => 'a', 'า' => 'a', 'ำ' => 'am', 'ิ' => 'i', 'ี' => 'i',
            'ึ' => 'ue', 'ื' => 'ue', 'ุ' => 'u', 'ู' => 'u', 'เ' => 'e', 'แ' => 'ae',
            'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai', 'ๅ' => 'ue', 'ฤ' => 'rue', 'ฦ' => 'lue',
            'ๆ' => '', 'ฯ' => '',
            '่' => '', '้' => '', '๊' => '', '๋' => '', '์' => '', '็' => '', 'ํ' => '', 'ฺ' => '',
            '๐' => '0', '๑' => '1', '๒' => '2', '๓' => '3', '๔' => '4',
            '๕' => '5', '๖' => '6', '๗' => '7', '๘' => '8', '๙' => '9',
        ];

        return strtr($value, $characters);
    }
}
