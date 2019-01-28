<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 26.01.2019
 * Time: 21:52
 */

namespace app\models;

/**
 * Class Selector Модель проводит проверку переданных ресурсов и выбирает один из них
 */
class Selector
{
    /**
     * Метод выполняет выполняет проверку переданных ресурсов.
     * 1.Проверяет на соответствие условиям
     * 2.Перебирает ресурсы по очереди на соответствие условиям
     * 3.Выбирает первый из доступных ресурсов, который прошел все перечисленные проверки
     * 
     * @return array содержит массив (ключ - формат данных, значение: если JSON - object(stdClass), если XML - object(SimpleXMLElement) ),
     * либо массив с сообщением об ошибке.
     */
    public static function validateAndSelect($source = null){
        if($source === null){
            $error['errorMessage'] = 'Не передан ресурс';
            return $error;
        }
        for($i = 0; $i<count($source); $i++ ){
            // проверяем на соответствие URL
            if(filter_var($source[$i], FILTER_VALIDATE_URL)) {
                // проверяем, соответствует ли расширение имеющегося файла шаблону
                $patternJSON = '~.\.(js|json)$~';
                $str = $source[$i];
                $resultJSON = preg_match($patternJSON, $str);
                if($resultJSON == 1 || $resultJSON == true ) {
                    // пробуем подгрузить файл
                    $json = file_get_contents($source[$i]);
                    if($json != false){
                        // декодируем строку JSON
                        $jsonObject = json_decode($json);
                        if($jsonObject != null){
                            $result['json'] = $jsonObject;
                            return $result;
                        }
                    }
                }
                else{
                    // проверяем, соответствует ли расширение имеющегося файла шаблону
                    $patternXML = '~.\.xml$~';
                    $str = $source[$i];
                    $resultXML = preg_match($patternXML, $str);
                    if($resultXML == 1 || $resultXML == true){
                        // Преобразуем XML в объект
                        $sxml = simplexml_load_file($source[$i]);
                        if($sxml != false){
                            $result['xml'] = $sxml;
                            return $result;
                        }
                    }
                }
            }
            else {
                $error['errorMessage'] = 'Ресурс не прошел проверку на URL';
                return $error;
            }
        }
        $error['errorMessage'] = 'Ни один ресурс не является JSON или XML документом';
        return $error;
    }

}