<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2016, Christoph Marti

  Author translation: Klimentiy Ranchukov.
  E-mail: strag@bk.ru
  
  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/


// PAYMENT METHOD ADVANCE PAYMENT
// ******************************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'Предоплата';

// USED BY FILE bakery/payment_methods/advance/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Предоплата';
$MOD_BAKERY[$payment_method]['TXT_ACCOUNT'] = 'Пожалуйста оплатите заказанные товары на наш расчетный счет или карточку.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Я оплачу сейчас';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Мы вышлем вам подтверждение заказа с информацией об оплате.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Как только мы получим оплату, мы отправим Вам заказанные товары.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Подтверждение и счет на оплату Вашего заказа на [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Уважаемые [CUSTOMER_NAME]

Благодарим Вас за покупки в [SHOP_NAME].
Ниже Вы найдете информацию о товарах которые Вы заказали:
[ITEM_LIST]

Пожалуйста оплатите заказанные товары на наш расчетный счет или карточку.
[BANK_ACCOUNT]

Как только мы получим оплату, мы отправим Вам заказанные товары:

[ADDRESS]


Спасибо за доверие.

С уважением,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Новый заказ на [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Дорогие [SHOP_NAME] Администратор

НОВЫЙ ЗАКАЗ НА [SHOP_NAME]:
	Заказ #: [ORDER_ID]
	Способ оплаты: предоплата

Адрес доставки:
[ADDRESS]

Адрес выставления счета:
[CUST_ADDRESS]

Список заказанных товаров:
[ITEM_LIST]


Сообщение покупателя:
[CUST_MSG]


С уважением,
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
