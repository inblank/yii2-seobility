<?php

namespace app\models;

use inblank\seobility\SeobilityBehavior;
use yii\db\ActiveRecord;

/**
 * Test model class with table name with {{%}}
 *
 * @property int $id
 * @property string $name
 *
 * @method void setSeobility($values, $condition = 0)
 * @method string[] getSeobility($condition = 0, $defaultIfNotFound = true, $defaultCondition = 0)
 * @method  array getAllSeobility($force = false)
 * @method  void deleteSeobility($condition = 0)
 * @method  void deleteAllSeobility()
 *
 * @package app\models
 */
class Model2 extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%model2}}';
    }

    function behaviors()
    {
        return [
            SeobilityBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
}
