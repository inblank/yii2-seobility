# Behavior for Yii2 to manage SEO data for ActiveRecord

[![Build Status](https://img.shields.io/travis/inblank/yii2-seobility/master.svg?style=flat-square)](https://travis-ci.org/inblank/yii2-seobility)
[![Packagist Version](https://img.shields.io/packagist/v/inblank/yii2-seobility.svg?style=flat-square)](https://packagist.org/packages/inblank/yii2-seobility)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/inblank/yii2-seobility/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-seobility/?branch=master)
[![Code Quality](https://img.shields.io/scrutinizer/g/inblank/yii2-seobility/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-seobility/?branch=master)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/inblank/yii2-seobility/master/LICENSE)

> **[Русская версия](https://github.com/inblank/yii2-seobility/blob/master/README_RU.md)** этого документа доступна [здесь](https://github.com/inblank/yii2-seobility/blob/master/README_RU.md).

Behavior `yii2-seobility` for Yii2 allows you to manage SEO data for ActiveRecord models. For each model, 
you can store multiple records with SEO data and select according to the condition. If data with the condition 
not found, then will returned the default data, and if them not, will returned the data with empty values.

Each SEO data entry contains the fields: `title`, `keywords` and `description`.


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Navigate to the project folder and run the console command:

```bash
$ composer require inblank/yii2-seobility
```

or add:

```json
"inblank/yii2-seobility": "~0.1"
```

to the `require` section of your `composer.json` file.


## Configuring

### Database

To storing SEO data the behavior uses an ActiveRecord model's database connection.
Behavior does not create and does not check for required tables. Tables must be created before using.

The name of the table to store and retrieve data, the behavior has on the basis of the table name of 
the ActiveRecord model to which is attached. The name is created by adding the suffix `_seo` to the 
table name of the ActiveRecord model. To get the table name of the model the model uses the 
method `ActiveRecord::tableName()`

> **Examples:** 
> - If the model uses a table `model`, the behaviour will use the table `model_seo`.
> - If the model uses a table `{{%model}}`, the behaviour will use the table `{{%model_seo}}`.

To create a table use the following SQL query, replacing `model_seo` by required table name:

> The query uses the [MySQL](http://dev.mysql.com/doc/refman/5.7/en/) syntax

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
 
### Model

To use a behavior just attach it to the ActiveRecord model as specified in the 
[Yii2 documentation](https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/concept-behaviors.md)
 
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
If you correctly created a table to store the SEO data, configuring is complete.


## Usage

After successful configuration, you can use behavior methods to manage SEO data of ActiveRecord models. 

### Setting default SEO data

> Default SEO data have condition=0 

To setting default SEO data:
 
```php
$model = Model::findOne(1);

// set data by the behavior method
$model->setSeobility([
    'title' => 'SEO title for model', 
    'keywords' => 'model, seo, keywords, yii2', 
    'description' => 'Simple model with SEO data' 
]);

// ... or by direct access
$model->seobility = [
    'title' => 'SEO title for model', 
    'keywords' => 'model, seo, keywords, yii2', 
    'description' => 'Simple model with SEO data' 
];

// not necessarily set all field for SEO data
// code below set only `title` field
$model->seobility = [
    'title' => 'Chnaged SEO title', 
];

// need to save model to store all setting SEO data
$model->save();
```
After saving the model, the default SEO data will contain defined values and these will be available at any time.

> **Note**: 
> If the model has not passed validation and was not saved SEO data will not be saved too. 

### Setting SEO data with condition

To setting SEO data with condition:
 
```php
$model = Model::findOne(1);

// setting a data with condition is only possible through the method of behavior
// set SEO data for condition=1
$model->setSeobility([
    'title' => 'Another SEO title', 
    'keywords' => 'model, seo, keywords, yii2', 
    'description' => 'This description is different' 
], 1);

// need to save model to store all setting SEO data
$model->save();
```
> **Note**: 
> If the model has not passed validation and was not saved SEO data will not be saved too. 

### Getting default SEO data

To getting default SEO data:

```php
$model = Model::findOne(1);

// getting default data through the method of behavior
$seo = $model->getSeobility();

// ... or by direct access
$seo = $model->seobility;
```

After getting the data, the variable `$seo` will contain an array with keys `title`, `keywords` and `description`.

Data will be obtained even if they were not specified. In this case, all array fields will contain empty value.

### Getting SEO data with condition

To getting SEO data with condition:

```php
$model = Model::findOne(1);

// getting data with condition is only possible through the method of behavior
$seo = $model->getSeobility(1);
```
Will be obtained SEO data with `condition=1`, and if no such data will be getting 
the default SEO data, or empty if no default data.

Through the method parameters, you can specify what data to obtain if the requested data is not found.

```php
// not get the default data if data with `condition=1` not found 
// i.e. if the data with `condition=1` will not be found, it returns 
// an array with empty values
$seo = $model->getSeobility(1, false);

// get data with `condition=2` if not found  data with `condition=1` 
// and if not found data with `condition=1` will return an array with empty values
$seo = $model->getSeobility(1, true, 2);
``` 

### Getting all SEO data

To getting all SEO data:

```php
$model = Model::findOne(1);

// getting all data
$seo = $model->getAllSeobility();
```

After getting the data, the variable `$seo` will contain an array with all the SEO data.
Indices of elements in the array are the values of the `condition`.

### Remove default SEO data

To remove default SEO data:
```php
$model = Model::findOne(1);

// remove default data
$seo = $model->deleteSeobility();
```
> **Attention**: 
> Be careful, the removal happens immediately and does not require `$model->save()` method.
> Removed data cannot be restore.

### Remove SEO data with condition

To remove SEO data with condition:
```php
$model = Model::findOne(1);

// remove data with condition=1
$seo = $model->deleteSeobility(1);
```
> **Attention**: 
> Be careful, the removal happens immediately and does not require `$model->save()` method.
> Removed data cannot be restore.

### Remove all SEO data for model

To remove all SEO data for model:
```php
$model = Model::findOne(1);

// remove ALL model's SEO data
$seo = $model->deleteAllSeobility();
```
> **Attention**: 
> Be careful, the removal happens immediately and does not require `$model->save()` method.
> Removed data cannot be restore.
