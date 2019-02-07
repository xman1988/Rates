<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 26.01.2019
 * Time: 23:49
 */

namespace app\models;

/**
 * Class Reader читает данные из источника и возвращает значения курса валют, либо возвращает сообщение об ошибке
 */
class Reader
{

    /**
     * Метод выполняет читает переданный ресурс в формате JSON, выбирает курс валюты по ключу 'EUR' и возвращает результат.
     * @return float содержит значение курса Евро/Рубль
     */
    public static function readJSON($jsonObject)
    {
        foreach ($jsonObject as $item) {
            if (is_object($item) == true) {
                foreach ($item as $key => $value) {
                    if ($key == 'EUR') {
                        $value = get_object_vars($value);
                        return $value['Value'];
                    }
                }
            }
        }
    }

    /**
     * Метод выполняет читает переданный ресурс в формате XML, выбирает курс валюты по ключу 'RUB' и возвращает результат.
     * @return object SimpleXMLElement содержит значение курса Евро/Рубль
     */
    public static function readXML($sxmlObject)
    {
        $arr = $sxmlObject->Cube->Cube->Cube;
        foreach ($arr as $value) {
            if ($value['currency'] == 'RUB') {
                return $value['rate'];
            }
        }
    }
}