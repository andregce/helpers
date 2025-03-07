<?php

namespace Sysvale;

class Helpers
{
    public static function maskBank($value)
    {
        if (strlen($value) <= 1) {
            return $value;
        }

        $last_digit = $value[strlen($value)-1];
        $value[strlen($value)-1] = '-';
        $value .= $last_digit;
        return $value;
    }


    public static function maskCpf($value)
    {
        $value = preg_replace('/\D/', '', $value);
        if (strlen($value) < 11) {
            return null;
        }
        return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split($value));
    }

    public static function unMaskCpf($value)
    {
        $value = preg_replace('/\D/', '', $value);
        return $value;
    }

    public static function maskPhone($value, $field = false)
    {
        $value = preg_replace('/\D/', '', $value);
        if (strlen($value) == 10) {
            return vsprintf("(%s%s) %s%s%s%s-%s%s%s%s", str_split($value));
        }

        if (strlen($value) == 11) {
            return vsprintf("(%s%s) %s%s%s%s%s-%s%s%s%s", str_split($value));
        }

        if ($field) {
            return "(___) ______-______";
        }

        return null;
    }

    public static function maskMoney($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public static function maskCep($value)
    {
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) == 8) {
            return vsprintf("%s%s%s%s%s-%s%s%s", str_split($value));
        }

        return null;
    }

    public static function maskCnpj($value)
    {
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) == 14) {
            return vsprintf("%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s", str_split($value));
        }

        return null;
    }

    public static function trimpp($str)
    {
        return preg_replace('/\s+/', ' ', trim($str));
    }

    public static function titleCase(
        $string,
        $delimiters = [' ', '-', '.', '\'', 'O\'', 'Mc'],
        $exceptions = [
            'da',
            'das',
            'de',
            'do',
            'dos',
            'e',
            'o',
            'and',
            'to',
            'of',
            'ou',
            'no',
            'com',
            'em',
            'sem',
            'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
        ]
    ) {
        /*
        * Exceptions in lower case are words you don't want converted
        * Exceptions all in upper case are any words you don't want converted to title case
        * but should be converted to upper case, e.g.:
        * king henry viii or king henry Viii should be King Henry VIII
        */
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);

            $newwords = array();

            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }

                array_push($newwords, $word);
            }

            $string = join($delimiter, $newwords);
        }//foreach

        return $string;
    }

    public static function firstUpper($str)
    {
        return titleCase($str);
    }

    public static function urlNoCache($url)
    {
        return "$url?_=". time();
    }

    public static function ptDate2IsoDate($date)
    {
        $fDate = explode('/', $date);

        if (count($fDate) < 3) {
            $fDate = $date;
        } else {
            if (strlen($fDate[2])<4) {
                if (intval($fDate[2])>30) {
                    $fDate[2]='19'.$fDate[2];
                } else {
                    $fDate[2]='20'.$fDate[2];
                }
            }

            $fDate = (new \DateTime($fDate[2] . '-' . $fDate[1] . '-' . $fDate[0]))->format('Y-m-d');
        }

        return $fDate;
    }

    public static function regexAccents($value)
    {
        $value = mb_strtolower($value, 'UTF-8');
        // letras àáâãäåæ
        $value = str_replace(['a', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ'], 'X', $value);
        $value = str_replace('X', '[a|à|á|â|ã|ä|å|æ]', $value);

        // letras èéêëẽ
        $value = str_replace(['e', 'è', 'é', 'ê', 'ẽ'], 'X', $value);
        $value = str_replace('X', '[e|è|é|ê|ẽ]', $value);

        // letras ìíîïĩ
        $value = str_replace(['i', 'ì', 'í', 'î', 'ï', 'ĩ'], 'X', $value);
        $value = str_replace('X', '[i|ì|í|î|ï|ĩ]', $value);

        // letras ðòóôõöø
        $value = str_replace(['o', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø'], 'X', $value);
        $value = str_replace('X', '[o|ð|ò|ó|ô|õ|ö|ø]', $value);

        // letras ùúûü
        $value = str_replace(['u', 'ù', 'ú', 'û', 'ü'], 'X', $value);
        $value = str_replace('X', '[u|ù|ú|û|ü]', $value);

        // letras ñ
        $value = str_replace(['n', 'ñ'], 'X', $value);
        $value = str_replace('X', '[n|ñ]', $value);

        // letras ç
        $value = str_replace(['c', 'ç'], 'X', $value);
        $value = str_replace('X', '[c|ç]', $value);

        // letras ýÿ
        $value = str_replace(['y', 'ý', 'ÿ'], 'X', $value);
        $value = str_replace('X', '[y|ý|ÿ]', $value);

        return $value;
    }

    public static function toInt($data)
    {
        if (is_array($data)) {
            $data = count($data) ? $data[0] : '';
        }

        return (isset($data) && strlen(strval($data))) ? intval($data) : null;
    }

    public static function toFloat($data)
    {
        if (is_array($data)) {
            $data = count($data) ? $data[0] : '';
        }

        return (isset($data) && strlen(strval($data))) ? floatval(str_replace(',', '.', $data)) : null;
    }

    public static function toTime($d, $t)
    {
        if (strpos($d, '00') === 0) {
            $d = '19' . substr($d, 2);
        }

        if (strlen($t) == 5) {
            $t = "$t:00";
        }

        $time = strlen($d)*strlen($t) ? strtotime("$d $t") : null;

        if ($time > time()) {
            $time = time();
        } elseif ($time < time()-130*365*24*60*60) {
            $time = time()-129*365*24*60*60;
        }

        return $time * 1000;
    }

    public static function toArray($r)
    {
        return isset($r) ? (array)$r : null;
    }

    public static function toArrayInt($arr)
    {
        $arr = to_array($arr);

        if (!$arr) {
            return null;
        }

        foreach ($arr as $key => $value) {
            $arr[$key] = intval($value);
        }
        return $arr;
    }

    public static function toData($d)
    {
        if (is_array($d)) {
            $d = count($d) ? $d[0] : null;
        }

        $d = ((isset($d) && strlen((string) $d)) || $d === false) ? $d : null;

        $d = ($d === 'true' ? true : $d);

        $d = ($d === 'false' ? false : $d);

        return $d;
    }

    public static function toBool($d)
    {
        if ($d === 'true' || $d === true) {
            return true;
        }

        if ($d === 'false' || $d === false) {
            return false;
        }

        return null;
    }

    public static function toBoolNotNull($d)
    {
        if ($d === 'true' || $d === true) {
            return true;
        }

        return false;
    }

    public static function removeAccents($name)
    {
        return strtr(
            utf8_decode($name),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
    }

    public static function compareVersion($v1, $signal, $v2 = '')
    {
        if (empty($v2)) {
            $v2 = $signal;
            $signal = '=';
        }

        $v1 = explode('.', $v1);

        $v2 = explode('.', $v2);

        for ($i=0; $i < 3; $i++) {
            $n1 = (int) preg_replace('/\D/', '', isset($v1[$i]) && !empty($v1[$i]) ? $v1[$i] : 0);
            $n2 = (int) preg_replace('/\D/', '', isset($v2[$i]) && !empty($v2[$i]) ? $v2[$i] : 0);

            switch ($signal) {
                case '=':
                case '==':
                    if ($n1 != $n2) {
                        return false;
                    }

                    break;
                case '<':
                    if ($n1 < $n2) {
                        return true;
                    }

                    if ($i < 2 && $n1 == $n2) {
                        continue;
                    } elseif ($n1 >= $n2) {
                        return false;
                    }

                    break;
                case '>':
                    if ($n1 > $n2) {
                        return true;
                    }

                    if ($i < 2 && $n1 == $n2) {
                        continue;
                    } elseif ($n1 <= $n2) {
                        return false;
                    }

                    break;
            }
        }
        return true;
    }

    public static function monthPt($value)
    {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        $value = (int) $value;

        return 1 <= $value && $value <= 12 ? $months[$value] : '';
    }

    public static function removeCrassLetters($str)
    {
        $search = ['à', 'è', 'ì', 'ò', 'ù', 'À', 'È', 'Ì', 'Ò', 'Ù'];
        $replace = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];
        return str_replace($search, $replace, $str);
    }

    public static function validateCpf($cpf)
    {
        $invalid_cpf_arr = [
            '00000000000',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999'
        ];

        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) != 11 || in_array($cpf, $invalid_cpf_arr)) {
            return false;
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf[$c] != $d) {
                    return false;
                }
            }

            return true;
        }
    }

    public static function weekDay($week_day_number)
    {
        $days = [
            1 => "Segunda-feira",
            2 => "Terça-feira",
            3 => "Quarta-feira",
            4 => "Quinta-feira",
            5 => "Sexta-feira",
            6 => "Sábado",
            7 => "Domingo",
        ];

        return $days[$week_day_number];
    }

    public static function city()
    {
        $database_id = \Crypt::decrypt(session('database_id'));

        return \App\Database::find($database_id)->city;
    }

    public static function getNFirstWords($str, $separator, $n)
    {
        $statement = explode($separator, $str);

        $out_str = '';

        if (count($statement) == 1) {
            return $statement[0];
        }

        for ($i = 0; $i < $n; $i++) {
            if ($i == $n - 1) {
                $out_str .= $statement[$i];
            } else {
                $out_str .= $statement[$i] . ' ';
            }
        }

        return $out_str;
    }
}
