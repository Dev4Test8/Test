<?php
    /*
        Напишите на PHP функцию разбора SMS с кодом подтверждения от сервиса Яндекс.Деньги.
        Функция должна принимать текстовую строку и возвращать код подтверждения, сумму и кошелек.
        Речь идет о реальном сервисе Яндекс.Деньги. Если у вас нет кошелька, то воспользуйтесь эмулятором, генерирующим идентичные сообщения.
        Учтите, что порядок полей, пунктуация и слова со временем могут быть изменены.
    */


    /**
     * разбора SMS от сервиса Яндекс.Деньги
     * @param string $smsText
     *
     * @return array
     */
    function parseYandexSms($smsText)
    {
        // если строка не юникод - то конвертировать бы по хорошему
        $code = $sum = $koshelek = null;
        $ar = preg_split("/[\n]+/", $smsText);
        $patternNumber = "/([0-9,]+)+/ui";
        $patternSum = "/(р\.)|(руб)/ui";

        foreach ($ar as $item) {

            preg_match_all($patternNumber, $item, $matches);
            if (isset($matches[0][0])) {
                $val = $matches[0][0];
                $len = mb_strlen($val);

                // если среди цифр есть запятая - то полюбому сумма
                if (mb_strpos($val, ',') !== false) {
                    $sum = $val;
                } // длинее 12 символов - 100% счет
                elseif ($len > 12) {
                    $koshelek = $val;
                } else {
                    if (!$sum) {// если сумма не обнаружена, возможно она указана без копеек

                        preg_match_all($patternSum, $item, $matches);
                        if (count($matches[0])) {
                            $sum = $val;
                            continue;
                        }
                    }
                    // бывало что получал 6ти значный код
                    if ($len > 3 && $len < 7) {
                        $code = $val;
                    }
                }
            }

        }


        return [$code, $sum, $koshelek];
    }

    $smsText = 'Пароль: 0009
Спишется 50,26 РУБ. р.
Перевод на счет 4100198176118';

    $res = parseYandexSms($smsText);
    print_r($res);