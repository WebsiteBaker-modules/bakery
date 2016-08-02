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



/*
  ***********************
  TRANSLATORS PLEASE NOTE
  ***********************
  
  Thank you for translating Bakery!
  Include your credits in the header of this file right above the licence terms.
  Please post your localisation file on the WB forum at http://www.websitebaker.org/forum/

*/



/*
  ****************
  Russian
  ****************
*/

// MODUL DESCRIPTION
$module_description = 'Bakery is a WebsiteBaker shop module with catalog, cart, stock administration, order administration and invoice print feature. Payment in advance, invoice, cash on delivery and/or different payment gateways. Further information can be found on the <a href="http://www.bakery-shop.ch" target="_blank">Bakery Website</a>.';

// ADDRESS FORM VALIDATION
// Besides of latin define additional charset to be accepted by the customer address form validation
// The charset must match the localisation language
$MOD_BAKERY['ADD_CHARSET'] = 'Cyrillic';

// MODUL BAKERY VARIOUS TEXT
$MOD_BAKERY['TXT_SETTINGS'] = 'Настройки';
$MOD_BAKERY['TXT_GENERAL_SETTINGS'] = 'Главные Настройки';
$MOD_BAKERY['TXT_PAGE_SETTINGS'] = 'Настройки страницы';
$MOD_BAKERY['TXT_PAYMENT_METHODS'] = 'Методы Оплаты';
$MOD_BAKERY['TXT_SHOP'] = 'Магазин';
$MOD_BAKERY['TXT_PAYMENT'] = 'Оплата';
$MOD_BAKERY['TXT_EMAIL'] = 'Email';
$MOD_BAKERY['TXT_LAYOUT'] = 'Макет';
$MOD_BAKERY['TXT_PAGE_OFFLINE'] = 'Установки страницы в режиме оффлайн';
$MOD_BAKERY['TXT_OFFLINE_TEXT'] = 'Оффлайн Текст';
$MOD_BAKERY['TXT_CONTINUE_URL'] = 'Продолжить покупки URL-адрес';
$MOD_BAKERY['TXT_OVERVIEW'] = 'Включить в каталоге';
$MOD_BAKERY['TXT_DETAIL'] = 'Включить в каталоге пункт Подробнее';
$MOD_BAKERY['TXT_SHOP_NAME'] = 'Название Магазина';
$MOD_BAKERY['TXT_TAC_URL'] = 'Сроки &amp; условия URL';
$MOD_BAKERY['TXT_SHOP_EMAIL'] = 'Email Магазина';
$MOD_BAKERY['TXT_SHOP_COUNTRY'] = 'Страна Магазина';
$MOD_BAKERY['TXT_SHOP_STATE'] = 'Область и район';
$MOD_BAKERY['TXT_ADDRESS_FORM'] = 'Форма для заполнения Адреса';
$MOD_BAKERY['TXT_SHIPPING_FORM_REQUEST'] = 'по запросу';
$MOD_BAKERY['TXT_SHIPPING_FORM_HIDEABLE'] = 'скрыт';
$MOD_BAKERY['TXT_SHIPPING_FORM_ALWAYS'] = 'всегда показвать';
$MOD_BAKERY['TXT_SHOW_COMPANY_FIELD'] = 'Показать поле Компания';
$MOD_BAKERY['TXT_SHOW_STATE_FIELD'] = 'Показать поле Область';
$MOD_BAKERY['TXT_SHOW_TAX_NO_FIELD'] = 'Показать поле НДС';
$MOD_BAKERY['TXT_SHOW_ZIP_END_OF_ADDRESS'] = 'Почтовый индекс и ваш Адрес';
$MOD_BAKERY['TXT_CUST_MSG'] = 'Сообщение от покупателя';
$MOD_BAKERY['TXT_SHOW_TEXTAREA'] = 'Показать текстовое поле';
$MOD_BAKERY['TXT_ALLOW_OUT_OF_STOCK_ORDERS'] = 'Разрешить клиентам оформлять заказ, если заказываемых товаров на данный момент нет на складе';
$MOD_BAKERY['TXT_SKIP_CART_AFTER_ADDING_ITEM'] = 'Пропустить просмотр корзины после добавления товара в корзину';
$MOD_BAKERY['TXT_MINICART_STRONGLY_RECOMMENDED'] = 'MiniCart настоятельно рекомендуется';
$MOD_BAKERY['TXT_DISPLAY_SETTINGS_TO_ADMIN_ONLY'] = 'Параметры отображения для администратора (id = 1) only';
$MOD_BAKERY['TXT_FREE_DEFINABLE_FIELD'] = 'Произвольное поле';
$MOD_BAKERY['TXT_STOCK_MODE_TEXT'] = 'Показать на складе покупателям в виде текста';
$MOD_BAKERY['TXT_STOCK_MODE_IMAGE'] = 'Показать на складе покупателям в виде изображения';
$MOD_BAKERY['TXT_STOCK_MODE_NUMBER'] = 'Показать на складе покупателям, как номер';
$MOD_BAKERY['TXT_STOCK_MODE_NONE'] = 'Не показывать запас покупателям';
$MOD_BAKERY['TXT_SHOP_CURRENCY'] = 'Код валюты магазина';
$MOD_BAKERY['TXT_SEPARATOR_FOR'] = 'Разделитель';
$MOD_BAKERY['TXT_DECIMAL'] = 'Десятичная дробь';
$MOD_BAKERY['TXT_GROUP_OF_THOUSANDS'] = 'Группа из тысячи';

$MOD_BAKERY['TXT_PAYMENT_METHOD'] = 'Методы Оплаты';
$MOD_BAKERY['TXT_SELECT_PAYMENT_METHOD'] = 'Выбрать метод оплаты';
$MOD_BAKERY['TXT_SELECT_PAYMENT_METHODS'] = 'Выбрать методы оплаты';
$MOD_BAKERY['TXT_PAYMENT_METHOD_COD'] = 'Оплата при доставке';
$MOD_BAKERY['TXT_PAYMENT_METHOD_BOPIS'] = 'Купить онлайн, забрать в магазине';
$MOD_BAKERY['TXT_PAYMENT_METHOD_ADVANCE'] = 'Предоплата';
$MOD_BAKERY['TXT_PAYMENT_METHOD_INVOICE'] = 'Выставленный счет';
$MOD_BAKERY['TXT_PAYMENT_METHOD_PAYMENT_NETWORK'] = 'Банковской системой SOFORT';
$MOD_BAKERY['TXT_NO_PAYMENT_METHOD_SETTING'] = 'У выбраных способов оплаты нет параметров для настройки.';
$MOD_BAKERY['TXT_NOTICE'] = 'Уведомление';
$MOD_BAKERY['TXT_DAYS'] = 'Дней';

$MOD_BAKERY['TXT_TAX_RATE'] = 'Налоговая ставка';
$MOD_BAKERY['TXT_SAVED_TAX_RATE'] = 'В настоящее время сохранена налоговая ставка';
$MOD_BAKERY['TXT_SET_TAX_RATE'] = 'Установить налоговую ставку';
$MOD_BAKERY['TXT_TAX_INCLUDED'] = 'Цены вкл. налог';
$MOD_BAKERY['TXT_TAX_GROUP'] = 'Налогове зоны стран ЕС';
$MOD_BAKERY['TXT_DOMESTIC'] = 'внутренняя';
$MOD_BAKERY['TXT_ZONE_COUNTRIES'] = 'к конкретным странам (множественный выбор)';
$MOD_BAKERY['TXT_ABROAD'] = 'за границу';
$MOD_BAKERY['TXT_PER_ITEM'] = 'за продукт';
$MOD_BAKERY['TXT_SHIPPING_BASED_ON'] = 'Доставка на основе';
$MOD_BAKERY['TXT_SHIPPING_METHOD_FLAT'] = 'фиксированной суммы';
$MOD_BAKERY['TXT_SHIPPING_METHOD_ITEMS'] = 'Количестве элементов';
$MOD_BAKERY['TXT_SHIPPING_METHOD_POSITIONS'] = 'Количестве позиций';
$MOD_BAKERY['TXT_SHIPPING_METHOD_PERCENTAGE'] = 'Процент Итого';
$MOD_BAKERY['TXT_SHIPPING_METHOD_HIGHEST'] = 'Пункт с наивысшим Стоимость доставки';
$MOD_BAKERY['TXT_SHIPPING_METHOD_NONE'] = 'ничего';
$MOD_BAKERY['TXT_FREE_SHIPPING'] = 'Бесплатная доставка';
$MOD_BAKERY['TXT_OVER'] = 'от';
$MOD_BAKERY['TXT_SHOW_FREE_SHIPPING_MSG'] = 'Информировать клиентов о ограничениях бесплатной доставки';
$MOD_BAKERY['TXT_EMAIL_SUBJECT'] = 'Email Subject';
$MOD_BAKERY['TXT_EMAIL_BODY'] = 'E-mail человека';
$MOD_BAKERY['TXT_ITEM'] = 'Товар';
$MOD_BAKERY['TXT_ITEMS'] = 'Товаров';
$MOD_BAKERY['TXT_ITEMS_PER_PAGE'] = 'Товаров на странице';
$MOD_BAKERY['TXT_NUMBER_OF_COLUMNS'] = 'Число столбцов';
$MOD_BAKERY['TXT_USE_CAPTCHA'] = 'Использовать Captcha';
$MOD_BAKERY['TXT_MODIFY_THIS'] = 'Сохранить настройки <b>этой</b> страницы магазина.';
$MOD_BAKERY['TXT_MODIFY_ALL'] = 'Сохранить настройки (without &quot;Continue Shopping URL&quot;) для <b>всех</b> страниц магазина.';
$MOD_BAKERY['TXT_MODIFY_MULTIPLE'] = 'Сохранить настройки (without &quot;Continue Shopping URL&quot;) для <b>выбраных</b> страниц магазина (Множественный Выбор):';

$MOD_BAKERY['TXT_ADD_ITEM'] = 'Добавить Товар';
$MOD_BAKERY['TXT_NAME'] = 'Название Товара';
$MOD_BAKERY['TXT_SKU'] = 'SKU#';
$MOD_BAKERY['TXT_PRICE'] = 'Цена';
$MOD_BAKERY['TXT_OPTION_NAME'] = 'Вариант названия';
$MOD_BAKERY['TXT_OPTION_ATTRIBUTES'] = 'Вариант Атрибутов';
$MOD_BAKERY['TXT_OPTION_PRICE'] = 'Варианты Цен';
$MOD_BAKERY['TXT_ITEM_OPTIONS'] = 'Вариативные товары';
$MOD_BAKERY['TXT_EG_OPTION_NAME'] = 'например. цвет';
$MOD_BAKERY['TXT_EG_OPTION_ATTRIBUTE'] = 'например. красный';
$MOD_BAKERY['TXT_INCL'] = 'Включительно';
$MOD_BAKERY['TXT_EXCL_SHIPPING'] = 'за исключением доставки';
$MOD_BAKERY['TXT_EXCL_SHIPPING_TAX'] = 'за исключением доставки и налогов';
$MOD_BAKERY['TXT_TAX'] = 'Налог';
$MOD_BAKERY['TXT_QUANTITY'] = 'Колличество';
$MOD_BAKERY['TXT_SUM'] = 'Сумма';
$MOD_BAKERY['TXT_SUBTOTAL'] = 'Подитог';
$MOD_BAKERY['TXT_TOTAL'] = 'Всего';
$MOD_BAKERY['TXT_SHIPPING'] = 'Доставка';
$MOD_BAKERY['TXT_SHIPPING_COST'] = 'Доставка';
$MOD_BAKERY['TXT_DESCRIPTION'] = 'Краткое описание';
$MOD_BAKERY['TXT_FULL_DESC'] = 'Полное описание';
$MOD_BAKERY['TXT_PREVIEW'] = 'Предварительный просмотр';
$MOD_BAKERY['TXT_FILE_NAME'] = 'Имя файла';
$MOD_BAKERY['TXT_MAIN_IMAGE'] = 'Главное Изображение';
$MOD_BAKERY['TXT_THUMBNAIL'] = 'Миниатюра';
$MOD_BAKERY['TXT_CAPTION'] = 'Подпись';
$MOD_BAKERY['TXT_POSITION'] = 'Позиция';
$MOD_BAKERY['TXT_IMAGE'] = 'Изображение';
$MOD_BAKERY['TXT_IMAGES'] = 'Изображения';
$MOD_BAKERY['TXT_MAX_WIDTH'] = 'Максимум. Ширина (px)';
$MOD_BAKERY['TXT_MAX_HEIGHT'] = 'Максимум. Высота (px)';
$MOD_BAKERY['TXT_JPG_QUALITY'] = 'JPG Качество';
$MOD_BAKERY['TXT_NON'] = 'нет';
$MOD_BAKERY['TXT_ITEM_TO_PAGE'] = 'Переместить элемент на страницу';
$MOD_BAKERY['TXT_MOVE'] = 'Двигать';
$MOD_BAKERY['TXT_DUPLICATE'] = 'Дублировать';

$MOD_BAKERY['TXT_CART'] = 'Корзина';
$MOD_BAKERY['TXT_ORDER'] = 'Заказ';
$MOD_BAKERY['TXT_ORDER_ID'] = 'Заказ#';
$MOD_BAKERY['TXT_INVOICE_ID'] = 'Выставленный счет#';
$MOD_BAKERY['TXT_CONTINUE_SHOPPING'] = 'Продолжить покупки';
$MOD_BAKERY['TXT_ADD_TO_CART'] = 'Добавить в корзину';
$MOD_BAKERY['TXT_VIEW_CART'] = 'Просмотреть корзину';
$MOD_BAKERY['TXT_UPDATE_CART'] = 'Обновить корзину';
$MOD_BAKERY['TXT_UPDATE_CART_SUCCESS'] = 'Корзина была успешно обновлена.';
$MOD_BAKERY['TXT_SUBMIT_ORDER'] = 'Подтвердить заказ';
$MOD_BAKERY['TXT_BUY'] = 'Купить';
$MOD_BAKERY['TXT_CANCEL_ORDER'] = 'Отменить заказ';
$MOD_BAKERY['TXT_ORDER_SUMMARY'] = 'Просмотреть и оформить заказ';

$MOD_BAKERY['TXT_ADDRESS'] = 'Адрес';
$MOD_BAKERY['TXT_MODIFY_ADDRESS'] = 'Изменить адрес';
$MOD_BAKERY['TXT_FILL_IN_ADDRESS'] = 'Пожалуйста, заполните в свой адрес';
$MOD_BAKERY['TXT_SHIP_ADDRESS'] = 'Адрес доставки';
$MOD_BAKERY['TXT_ADD_SHIP_FORM'] = 'Добавить адрес доставки';
$MOD_BAKERY['TXT_HIDE_SHIP_FORM'] = 'Скрыть Адрес доставки';
$MOD_BAKERY['TXT_FILL_IN_SHIP_ADDRESS'] = 'Пожалуйста, заполните адрес доставки';
$MOD_BAKERY['TXT_TAC'] = 'Условия и положения';
$MOD_BAKERY['TXT_AGREE'] = 'Я согласен с условиями';
$MOD_BAKERY['TXT_CANCEL'] = 'Вы отменили свой заказ.';
$MOD_BAKERY['TXT_DELETED'] = 'Все ваши данные были удалены.';
$MOD_BAKERY['TXT_THANK_U_VISIT'] = 'Спасибо за посещение!';

// MODUL BAKERY CUSTOMER DATA
$MOD_BAKERY['TXT_CUST_EMAIL'] = 'Электронная почта';
$MOD_BAKERY['TXT_CUST_CONFIRM_EMAIL'] = 'Подтвердить электронную почту';
$MOD_BAKERY['TXT_CUST_COMPANY'] = 'Компания';
$MOD_BAKERY['TXT_CUST_FIRST_NAME'] = 'Имя';
$MOD_BAKERY['TXT_CUST_LAST_NAME'] = 'Фамилия';
$MOD_BAKERY['TXT_CUST_TAX_NO'] = 'НДС нет';
$MOD_BAKERY['TXT_OPTIONAL'] = 'Необязательно';
$MOD_BAKERY['TXT_CUST_ADDRESS'] = 'Адрес';
$MOD_BAKERY['TXT_CUST_CITY'] = 'Город';
$MOD_BAKERY['TXT_CUST_STATE'] = 'Область';
$MOD_BAKERY['TXT_CUST_COUNTRY'] = 'Страна';
$MOD_BAKERY['TXT_CUST_ZIP'] = 'Индекс';
$MOD_BAKERY['TXT_CUST_PHONE'] = 'Телефон';

// MODUL BAKERY PROCESS PAYMENT
$MOD_BAKERY['TXT_TAC_AND_PAY_METHOD'] = 'Terms &amp; Conditions and Payment Method';
$MOD_BAKERY['TXT_ENTER_CUST_MSG'] = 'Вы можете отправить нам сообщение';
$MOD_BAKERY['TXT_SELECT_PAY_METHOD'] = 'Пожалуйста, выберите способ оплаты';
$MOD_BAKERY['TXT_SELECTED_PAY_METHOD'] = 'Выбран метод оплаты';
$MOD_BAKERY['TXT_MODIFY_PAY_METHODS'] = 'Измените метод оплаты';
$MOD_BAKERY['TXT_THANK_U_ORDER'] = 'Спасибо за ваш заказ!';

// MODUL BAKERY ORDER ADMINISTRATION
$MOD_BAKERY['TXT_ORDER_ADMIN'] = 'Администрирование Заказов';
$MOD_BAKERY['TXT_ORDER_ARCHIVED'] = 'Архив заказов';
$MOD_BAKERY['TXT_ORDER_CURRENT'] = 'Текущие заказы';

$MOD_BAKERY['TXT_CUSTOMER'] = 'Клиент';
$MOD_BAKERY['TXT_STATUS'] = 'Статус';
$MOD_BAKERY['TXT_ORDER_DATE'] = 'Дата заказа';
$MOD_BAKERY['TXT_EDIT_ORDER'] = 'Изменить данные клиента';

$MOD_BAKERY['TXT_STATUS_ORDERED'] = 'Заказ';
$MOD_BAKERY['TXT_STATUS_SHIPPED'] = 'Отправлен';
$MOD_BAKERY['TXT_STATUS_BUSY'] = 'Оплата в процессе';
$MOD_BAKERY['TXT_STATUS_INVOICE'] = 'Ожидает оплаты';
$MOD_BAKERY['TXT_STATUS_REMINDER'] = 'напоминание';
$MOD_BAKERY['TXT_STATUS_PAID'] = 'Оплата';
$MOD_BAKERY['TXT_STATUS_ARCHIVE'] = 'архив';
$MOD_BAKERY['TXT_STATUS_ARCHIVED'] = 'архивные';
$MOD_BAKERY['TXT_STATUS_CANCEL'] = 'отменить';
$MOD_BAKERY['TXT_STATUS_CANCELED'] = 'отмененный';

$MOD_BAKERY['TXT_PRINT'] = 'Печать';
$MOD_BAKERY['TXT_INVOICE'] = 'Выставленный счет';
$MOD_BAKERY['TXT_DELIVERY_NOTE'] = 'Накладная';
$MOD_BAKERY['TXT_REMINDER'] = 'Напоминание';
$MOD_BAKERY['TXT_PRINT_INVOICE'] = 'Печать счета-фактуры';

$MOD_BAKERY['TXT_SEND_INVOICE'] = 'Отправить счет в качестве HTML электронной почте.';
$MOD_BAKERY['TXT_INVOICE_ALREADY_SENT'] = 'Счет был отправлена %d раз.';
$MOD_BAKERY['TXT_INVOICE_HAS_BEEN_SENT_SUCCESSFULLY'] = 'Счет был отправлен заказчику успешно.';

// MODUL BAKERY STOCK ADMINISTRATION
$MOD_BAKERY['TXT_STOCK_ADMIN'] = 'Управление складом';
$MOD_BAKERY['TXT_STOCK'] = 'На складе';
$MOD_BAKERY['TXT_IN_STOCK'] = 'Количество';
$MOD_BAKERY['TXT_SHORT_OF_STOCK'] = 'В наличии';
$MOD_BAKERY['TXT_OUT_OF_STOCK'] = 'Товар закончился';
$MOD_BAKERY['TXT_N/A'] = 'Нет на складе';
$MOD_BAKERY['TXT_ALL'] = 'все';
$MOD_BAKERY['TXT_ORDER_ASC'] = 'по возрастанию';
$MOD_BAKERY['TXT_ORDER_DESC'] = 'порядке убывания';
$MOD_BAKERY['TXT_SHORT_OF_STOCK_SUBSEQUENT_DELIVERY'] = 'Этих товаров не хватает складе. <br /> Вы их получите последующей доставкой';
$MOD_BAKERY['TXT_SHORT_OF_STOCK_QUANTITY_CAPPED'] = 'Этих товаров не хватает складе - их количество было настроено';
$MOD_BAKERY['TXT_AVAILABLE_QUANTITY'] = 'пока недоступны';

// EDIT CSS BUTTON
$GLOBALS['TEXT']['CAP_EDIT_CSS'] = 'Редактировать CSS';

// MODUL BAKERY ERROR MESSAGES (Important: Do not remove <br /> !)
$MOD_BAKERY['ERR_INVALID_FILE_NAME'] = 'Недопустимое Имя файла';
$MOD_BAKERY['ERR_FILE_NAME_TOO_LONG'] = 'Имя файла слишком длинное';
$MOD_BAKERY['ERR_OFFLINE_TEXT'] = 'Магазин XXX-это автономный режим для обслуживания до ХХХ. Пожалуйста, зайдите позже.<br />Извините за доставленные неудобства.';
$MOD_BAKERY['ERR_NO_ORDER_ID'] = 'SKU не найден.';
$MOD_BAKERY['ERR_CART_EMPTY'] = 'Корзина покупок пуста.'; 
$MOD_BAKERY['ERR_ITEM_EXISTS'] = 'Этот товар уже присутствует в корзине.<br />Вы можете изменить количество в корзине.';
$MOD_BAKERY['ERR_QUANTITY_ZERO'] = 'Количество должно быть числом больше нуля!';
$MOD_BAKERY['ERR_FIELD_BLANK'] = 'Поля, выделенные красным, пустые. Пожалуйста, введите необходимую информацию.';
$MOD_BAKERY['ERR_EMAILS_NOT_MATCHED'] = 'Адреса электронной почты не совпадают!';
$MOD_BAKERY['ERR_INVAL_NAME'] = 'недопустимое имя.';
$MOD_BAKERY['ERR_INVAL_CUST_TAX_NO'] = 'не является допустимым НДС номером';
$MOD_BAKERY['ERR_INVAL_STREET'] = 'недопустимый адрес.';
$MOD_BAKERY['ERR_INVAL_CITY'] = 'недопустимый город.';
$MOD_BAKERY['ERR_INVAL_STATE'] = 'недопустимое состояние.';
$MOD_BAKERY['ERR_INVAL_COUNTRY'] = 'недопустимая страна.';
$MOD_BAKERY['ERR_INVAL_EMAIL'] = 'неправильный адрес электронной почты.';
$MOD_BAKERY['ERR_INVAL_ZIP'] = 'недопустимый почтовый индекс.';
$MOD_BAKERY['ERR_INVAL_PHONE'] = 'недопустимый телефон.';
$MOD_BAKERY['ERR_INVAL_TRY_AGAIN'] = 'Пожалуйста, проверьте ваши записи!';
$MOD_BAKERY['ERR_AGREE'] = 'Мы можем выполнить Ваш заказ, если вы согласны с нашими условиями.<br />спасибо за понимание!';
$MOD_BAKERY['ERR_NO_PAYMENT_METHOD'] = 'Нет способа оплаты активирован.';
$MOD_BAKERY['ERR_EMAIL_NOT_SENT'] = 'Не удается отправить почту. Ваш заказ остается в силе. Пожалуйста, свяжитесь с администратором магазина';

// MODUL BAKERY JAVASCRIPT MESSAGES (Important: Do not remove \n !)
$MOD_BAKERY['TXT_JS_CONFIRM'] = 'Вы действительно хотите отменить свой заказ?';
$MOD_BAKERY['TXT_JS_AGREE'] = 'Мы можем выполнить Ваш заказ, если вы согласны с нашими условиями.<br />спасибо за понимание!';
$MOD_BAKERY['TXT_JS_BLANK_CAPTCHA'] = 'Пожалуйста, введите проверочный номер (также известный как капча)!';
$MOD_BAKERY['TXT_JS_INCORRECT_CAPTCHA'] = 'Проверочный номер (также известный как капча) не совпадает.\ппожалуйста исправить вашу запись!';
$MOD_BAKERY['TXT_JS_CONFIRM_SEND_INVOICE'] = 'Вы хотите, отправить заказчику счета-фактуры?';