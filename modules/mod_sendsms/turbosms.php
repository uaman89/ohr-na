<?php
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
echo '
';

print_r ($client->__getFunctions ());

echo '
';

// Дані авторизації
$auth = Array (
'login' => 'ohrana_test',
'password' => '123123'
);

// Авторизуємося на сервері
$result = $client->Auth ($auth);

// Результат авторизації
echo $result->AuthResult . '
';

// Отримуємо кількість доступних кредитів
$result = $client->GetCreditBalance ();
echo $result->GetCreditBalanceResult . '
';

// Текст повідомлення ОБОВ'ЯЗКОВО відправляти в кодуванні UTF-8
$text = iconv ('windows-1251', 'utf-8', 'Это сообщение будет доставлено на указанный номер');

// Дані для відправки
$sms = Array (
'sender' => 'ohrana-tst',
'destination' => '+380977739918',
'text' => $text
);

// Відправляємо повідомлення на один номер.
// Підпис відправника може містити англійські букви і цифри. Максимальна довжина - 11 символів.
// Номер вказується в повному форматі, включно з плюсом і кодом країни
$result = $client->SendSMS ($sms);

// Виводимо результат відправлення.
echo '<br>$result->SendSMSResult->ResultArray[0]:';
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

echo '-----
';

// Запитуємо статус кожного повідомлення по ID
foreach ($result->GetNewMessagesResult->ResultArray as $msg_id) {
$sms = Array ('MessageId' => $msg_id);
$status = $client->GetMessageStatus ($sms);
echo '' . $msg_id . ' - ' . $status->GetMessageStatusResult . '
';
}
}
