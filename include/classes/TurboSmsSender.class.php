<?php
/**
* Даний приклад надає можливість відправляти СМС повідомлення
* з заміною номера, переглядати залишок кредитів користувача,
* переглядати статус відправлених повідомлень.
* -----------------------------------------------------------------
* Для роботи даного прикладу необхідно підключити SOAP-розширення.
*
*/


class TurboSmsSender{

    private static $client;

    private static function init(){
        // Підключаємося до серверу
        self::$client = new SoapClient ('http://turbosms.in.ua/api/wsdl.html');

        // Авторизуємося на сервері
        $auth = Array (
            'login' => 'ohrana_ua',
            'password' => 'ptashkinsv'
            //'login' => 'ohrana_test',
            //'password' => '123123'
        );
        $result = self::$client->Auth ($auth);
        return $result;
    }

//--- end init ---------------------------------------------------------------------------------------------------------

    private static function getClient(){
        //make Auth if we haven't yet.
        if ( empty(self::$client) ) self::init();

        return self::$client;
    }

//--- end init ---------------------------------------------------------------------------------------------------------


    public static function sendSMS( $recipients, $msg ){

        // Всі дані повертаються в кодуванні UTF-8
        header ('Content-type: text/html; charset=utf-8');

        $sms = Array (
            'sender' => 'Ohrana.ua',
            'destination' => $recipients,
            'text' => $msg
        );

        $client = self::getClient();

        // Відправляємо повідомлення на один номер.
        // Підпис відправника може містити англійські букви і цифри. Максимальна довжина - 11 символів.
        // Номер вказується в повному форматі, включно з плюсом і кодом країни

        $result = $client->SendSMS ($sms);

        //for debug:
//        var_dump($result->SendSMSResult);

        return $result->SendSMSResult->ResultArray;
    }

//--- end sendSMS ---------------------------------------------------------------------------------------------------------

    public static function getSmsStatus( $sms_id ){

        $client = self::getClient();
        $data = array('MessageId' => $sms_id);

//        var_dump($sms_id, $data);

        $res = $client->GetMessageStatus ( $data );
//        echo '$status->GetMessageStatusResult: '.$res->GetMessageStatusResult;
        return $res->GetMessageStatusResult;
    }

//--- end getSmsStatus ---------------------------------------------------------------------------------------------------------

    /**
     * makes given tel. number to format +380xxxxxxxxx
     *
     * @param $tel (string) - assumption that here only one tel. number
     * @return bool|string
     */
    public static function formatTelNumber( $tel ){
        preg_match_all('/\d+/', $tel, $matches);
        if (!empty($matches)){
            $tel = implode($matches[0]);

            //$tel = implode( '', $pieces );
            if ($tel[0]=='3') $tel = '+'.$tel;
            if ($tel[0]=='0') $tel = '+38'.$tel;
            
            return $tel;
        }
        else return false;
    }
//--- end formatTelNumber ---------------------------------------------------------------------------------------------------------

    static public function demo(){
        /**
         * Даний приклад надає можливість відправляти СМС повідомлення
         * з заміною номера, переглядати залишок кредитів користувача,
         * переглядати статус відправлених повідомлень.
         * -----------------------------------------------------------------
         * Для роботи даного прикладу необхідно підключити SOAP-розширення.
         *
         */

        // Всі дані повертаються в кодуванні UTF-8
        header ('Content-type: text/html; charset=utf-8');

        // Підключаємося до серверу
        $client = new SoapClient ('http://turbosms.in.ua/api/wsdl.html');

        // Можна переглянути список доступних функцій серверу

        print_r ($client->__getFunctions ());


        // Дані авторизації (sms шлюз)
        $auth = Array (
            //'login' => 'ohrana_test',
            //'password' => '123123'
            'login' => 'ohrana_ua',
            'password' => 'ptashkinsv'

        );

        // Авторизуємося на сервері
        $result = $client->Auth ($auth);

        // Результат авторизації
        echo $result->AuthResult . '<br>';

        // Отримуємо кількість доступних кредитів
        $result = $client->GetCreditBalance ();
        echo $result->GetCreditBalanceResult . '<br>';

        // Текст повідомлення ОБОВ'ЯЗКОВО відправляти в кодуванні UTF-8
        $text = iconv ('windows-1251', 'utf-8', 'Это сообщение будет доставлено на указанный номер');

        // Дані для відправки
        $sms = Array (
            'sender' => 'ohrana.ua',
            'destination' => '+380977739918',
            'text' => $text
        );

        // Відправляємо повідомлення на один номер.
        // Підпис відправника може містити англійські букви і цифри. Максимальна довжина - 11 символів.
        // Номер вказується в повному форматі, включно з плюсом і кодом країни
        $result = $client->SendSMS ($sms);

        // Виводимо результат відправлення.
        echo '<br>$result->SendSMSResult->ResultArray:';
        var_dump($result->SendSMSResult->ResultArray);

        /*
        // Відправляємо повідомлення на декілька номерів.
        // Номери розділено комами без пробілів.
        $sms = Array (
        'sender' => 'Rassilka',
        'destination' => '+380XXXXXXXX1,+380XXXXXXXX2,+380XXXXXXXX3',
        'text' => $text
        );
        $result = $client->SendSMS ($sms);

        // Виводимо результат відправлення.
        echo '<br>$result->SendSMSResult->ResultArray[0]:'.$result->SendSMSResult->ResultArray[0] . '
        ';

        // ID першого повідомлення
        echo $result->SendSMSResult->ResultArray[1] . '
        ';

        // ID другого повідомлення
        echo '<br>$result->SendSMSResult->ResultArray[0]:'.$result->SendSMSResult->ResultArray[2] . '
        ';

        // Відправляємо повідомлення з WAPPush посиланням
        // Посилання має включати http://
        $sms = Array (
        'sender' => 'Rassilka',
        'destination' => '+380977739918',
        'text' => $text,
        'wappush' => 'http://super-site.com'
        );

        $result = $client->SendSMS ($sms);
        */

        // Запитуємо статус конкретного повідомлення по ID
        $sms = Array ('MessageId' => 'c9482a41-27d1-44f8-bd5c-d34104ca5ba9');
        $status = $client->GetMessageStatus ($sms);
        echo '$status->GetMessageStatusResult: '.$status->GetMessageStatusResult . '
        ';

        // Запитуємо масив ID повідомлень, у яких невідомий статус відправлення
        $result = $client->GetNewMessages ();

        // Є повідомлення
        if (!empty ($result->GetNewMessagesResult->ResultArray)) {
            echo 'Є повідомлення:';

            print_r ($result->GetNewMessagesResult->ResultArray);

            echo '-----<br>';

            // Запитуємо статус кожного повідомлення по ID
            foreach ($result->GetNewMessagesResult->ResultArray as $msg_id) {
                $sms = Array ('MessageId' => $msg_id);
                $status = $client->GetMessageStatus ($sms);
                echo '' . $msg_id . ' - ' . $status->GetMessageStatusResult . '<br>';
            }
        }
    }
//--- end demo ---------------------------------------------------------------------------------------------------------

}




