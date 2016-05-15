# Поведение (Behavior) Yii2 для работы с SEO данными в ActiveRecord

[![Build Status](https://img.shields.io/travis/inblank/yii2-seobility/master.svg?style=flat-square)](https://travis-ci.org/inblank/yii2-seobility)
[![Packagist Version](https://img.shields.io/packagist/v/inblank/yii2-seobility.svg?style=flat-square)](https://packagist.org/packages/inblank/yii2-seobility)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/inblank/yii2-seobility/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-seobility/?branch=master)
[![Code Quality](https://img.shields.io/scrutinizer/g/inblank/yii2-seobility/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-seobility/?branch=master)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/inblank/yii2-seobility/master/LICENSE)

> The **[English version](https://github.com/inblank/yii2-seobility/blob/master/README.md)** of this document available [here](https://github.com/inblank/yii2-seobility/blob/master/README.md).

Поведение `yii2-seobility` для [Yii2](http://www.yiiframework.com/) позволяет управлять SEO данными 
для моделей ActiveRecord. Для каждой модели можно хранить несколько записей с SEO данными и выбирать необходимое 
по условию. Если не найдено данных отвечающих условию, то будут получены данные по умолчанию, а если их нет, 
то будут получены данные с пустыми значениями.

Каждая запись ы SEO данными содержит поля: `title`, `keywords` и `description`.


## Установка

Рекомендуется устанавливать поведение через [composer](http://getcomposer.org/download/).

Перейдите в папку проекта и выполните в консоли команду:

```bash
$ composer require inblank/yii2-seobility
```

или добавьте:

```json
"inblank/yii2-seobility": "~0.1"
```

в раздел `require` конфигурационного файла `composer.json`.


## Настройка

### База данных

Для хранения SEO данных поведение использует базу данных используемую моделью ActiveRecord к которой прикреплено.
Поведение не создает и не проверяет наличие необходимых таблицы, их нужно создать перед началом использования.

Имя таблицы, для хранения и получения данных, поведение формирует на основе имени таблицы модели ActiveRecord к
которому прикреплено. Имя формируется путем добавления суффикса `_seo` к имени таблицы модели ActiveRecord. 
Для получения имени таблицы модели используется метод модели `ActiveRecord::tableName()`

> **Примеры:** 
> - Если модель использует таблицу `model`, поведение будет использовать таблицу `model_seo`.
> - Если модель использует таблицу `{{%model}}`, поведение будет использовать таблицу `{{%model_seo}}`

Для создания таблицы используйте следующий SQL запрос, заменив `model_seo` на необходимое имя таблицы:

> В запросе используется синтаксиc [MySQL](http://dev.mysql.com/doc/refman/5.7/en/)

```sql
CREATE TABLE `model_seo`(
    `model_id` INT NOT NULL DEFAULT 0,
    `condition` INT NOT NULL DEFAULT 0,
    `title` TEXT NOT NULL,
    `keywords` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    
    PRIMARY KEY (`model_id`, `condition`)
);
```
 
### Модель

Для использования поведения просто подключите его к модели ActiveRecord как 
указано в [документации Yii2](https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-behaviors.md)
 
```php
use inblank\seobility\SeobilityBehavior;

/**
 * ...
 */
class Model extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            SeobilityBehavior::className(),
        ];
    }
}
```
Если Вы корректно создали таблицу для хранения SEO данных, настройка завершена.


## Использование

После успешной настройки можно использовать методы поведения для управления SEO данными ActiveRecord модели. 

### Задание для модели SEO данных по умолчанию

> SEO данные по умолчанию имеют condition=0 

Пример задания для модели SEO данных по умолчанию:
 
```php
$model = Model::findOne(1);

// задаем данные с помощью метода поведения
$model->setSeobility([
    'title' => 'SEO заголовк для модели', 
    'keywords' => 'модель, seo, ключевые, yii2', 
    'description' => 'Это простая модель с SEO данными' 
]);

// ... или через прямой доступ
$model->seobility = [
    'title' => 'SEO заголовк для модели', 
    'keywords' => 'модель, seo, ключевые, yii2', 
    'description' => 'Это простая модель с SEO данными' 
];

// не обязательно задавать все поля SEO данных
// следующий код заменит только поле title
$model->seobility = [
    'title' => 'Замена SEO заголовка', 
];

// нужно сохранить модель, чтобы были сохранение все установленные SEO данных
$model->save();
```
После сохранения модели, SEO данные по умолчанию будут содержать заданные значения и их можно будет получить в любой момент.

> **Примечание**: 
> Если модель, к которой прикреплено поведение, не прошла валидацию и не была сохранена,
> SEO данные так же не будут сохранены. 

### Задание для модели SEO данных с условием

Пример задания для модели SEO данных с условием:
 
```php
$model = Model::findOne(1);

// задание данных с условием возможно только через метод поведения
// задаем SEO данные для condition=1
$model->setSeobility([
    'title' => 'Совершенно другой SEO заголовок', 
    'keywords' => 'модель, seo, ключевые, yii2', 
    'description' => 'Это описание отлично от описания по умолчанию' 
], 1);

// нужно сохранить модель, чтобы были сохранение все установленные SEO данных
$model->save();
```
> **Примечание**: 
> Если модель, к которой прикреплено поведение, не прошла валидацию и не была сохранена, 
> SEO данные так же не будут сохранены. 

### Получение у модели SEO данных по умолчанию

Для получения у модели SEO данных по умолчанию:

```php
$model = Model::findOne(1);

// получаем данные с помощью метода поведения
$seo = $model->getSeobility();

// ... или через прямой доступ
$seo = $model->seobility;
```

После получения данных переменная `$seo` будет содержит массив с ключами `title`, `keywords` и `description`.

Данные будут получены даже если они не были заданы. В этом случае все поля массива будут 
содержать пустое значение.

### Получение у модели SEO данных с условием

Для получения у модели SEO данных с условием:

```php
$model = Model::findOne(1);

// получение данных с условием возможно только через метод поведения
$seo = $model->getSeobility(1);
```
Будут получены SEO данные с `condition=1`, а если таких данных нет, будут получены SEO данные по умолчанию,
либо пустые, если нет и данных по умолчанию.

Через параметры метода можно указать какие данные получать в случае отсутствия запрашиваемых данных.

```php
// не получать данные по умолчанию если не найдены данные с `condition=1`
// т.е. если данные с `condition=1` не будут найдены,
// то будет возвращен массив с пустыми значениями 
$seo = $model->getSeobility(1, false);

// получить данные с `condition=2` если не найдены данные с `condition=1`
// а если не будут найдены данные с `condition=1` будет возвращен массив
// с пустыми значениями
$seo = $model->getSeobility(1, true, 2);
``` 

### Получение всех SEO данных модели

Для получения всех SEO данных:

```php
$model = Model::findOne(1);

// получаем все данные модели
$seo = $model->getAllSeobility();
```

После получения данных переменная `$seo` будет содержать массив со всеми SEO данными модели.
Индексами элементов в массиве выступают значения условий `condition`.

### Удаление у модели SEO данных по умолчанию

Для удаления у модели SEO данных по умолчанию:
```php
$model = Model::findOne(1);

// удаляем данные по умолчани
$seo = $model->deleteSeobility();
```
> **Внимание**: 
> Будьте осторожны, удаление происходит сразу и не требует вызова метода `$model->save()`.
> Удаленные данные нельзя будет восстановить.

### Удаление у модели SEO данных с условием

Для удаления у модели SEO данных с условием:
```php
$model = Model::findOne(1);

// удаляем данные с условием condition=1
$seo = $model->deleteSeobility(1);
```
> **Внимание**: 
> Будьте осторожны, удаление происходит сразу и не требует вызова метода `$model->save()`.
> Удаленные данные нельзя будет восстановить.

### Удаление всех SEO данных модели

Для удаления всех SEO данных модели:
```php
$model = Model::findOne(1);

// удаляем ВСЕ данные
$seo = $model->deleteAllSeobility();
```
> **Внимание**: 
> Будьте осторожны, удаление происходит сразу и не требует вызова метода `$model->save()`.
> Удаленные данные нельзя будет восстановить.
