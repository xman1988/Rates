<?php
/**
 * Created by PhpStorm.
 */

namespace app\controllers;

use app\models\Reader;
use app\models\Selector;



/**
 * Class RateController Класс извлекает курс валют из сторонних источников(периодичность опроса источника 10 сек)
 * и выводит на страницу сайта(site/index).
 */
class RateController extends AppController
{
    /**
     * @var array $source Список сторонних ресурсов
     */
    public $source;

    /**
     * $rate Принимает значения курса валют или сообщение об ошибке
     */
    public $rate;

    /**
     * Метод выполняет выполняет функцию контроллера.
     * Проверяет ресурсы, принимает результат парсинга данных ресурсов, передает результат в вид.
     * @return array  Возвращает массив со значением курса валюты, либо строку с текстом ошибки
     */
    public function actionIndex()
    {
        $this->source = [
            'https://www.cbr-xml-daily.ru/daily_json.js',
            'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml'
        ];
        /**
         * $url array содержит массив (ключ - формат данных,
         * значение: если JSON - object(stdClass), если XML - object(SimpleXMLElement) ),
         * либо массив с сообщением об ошибке.
         */
        $url = Selector::validateAndSelect($this->source);

        if (isset($url['json'])) {
            $jsonObject = $url['json'];
            $this->rate = Reader::readJSON($jsonObject);

        } elseif (isset($url['xml'])) {
            $xmlObject = $url['xml'];
            $this->rate = Reader::readXML($xmlObject);


        }elseif(isset($url['errorMessage']) || isset($url['errorMessage'])){
            $error['validateURL'] = $url['errorMessage'];
            $error['isData'] = $url['errorMessage'];
            $this->rate = 'Нет данных';

        }

        /**
         * Задается MIME-тип ответа.
         */
           \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['rate' => $this->rate];

    }

    
}