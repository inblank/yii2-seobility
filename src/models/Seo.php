<?php
/**
 * Model for store and access SEO data for ActiveRecord in Yii2 framework
 *
 * @link https://github.com/inblank/yii2-seobility
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */

namespace inblank\seobility\models;

use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Model for store and access SEO data for ActiveRecord in Yii2 framework
 *
 * @package inblank\seobility
 */
class Seo extends Model
{
    /**
     * @var string suffix for build table name for the tables stored SEO data for ActiveRecord
     */
    static public $tableSuffix = 'seo';
    /**
     * @var int SEO data owner model id
     */
    public $model_id;
    /**
     * @var int SEO data condition
     */
    public $condition = 0;
    /**
     * @var string SEO title
     */
    public $title = '';
    /**
     * @var string SEO keywords
     */
    public $keywords = '';
    /**
     * @var string SEO description
     */
    public $description = '';
    /**
     * @var bool new record flag
     */
    protected $_isNewRecord;
    /**
     * @var ActiveRecord SEO data owner
     */
    protected $_owner;

    /**
     * Search SEO data. If not found, will be returned new SEO model with empty data
     * @param ActiveRecord $owner the ActiveRecord model for which to find data
     * @param int $condition optional condition for searched SEO data
     * @return self
     */
    public static function find(ActiveRecord $owner, $condition = 0)
    {
        $seo = new self();
        $seo->_owner = $owner;
        $condition = (int)$condition;
        $data = $owner->getIsNewRecord() ? false : (new Query())
            ->from(Seo::tableName($owner))
            ->limit(1)
            ->andWhere([
                'condition' => $condition,
                'model_id' => $owner->getPrimaryKey(),
            ])->one($owner->getDb());
        $seo->_isNewRecord = empty($data);
        if ($data) {
            $seo->setAttributes($data);
        }
        $seo->condition = $condition;
        return $seo;
    }

    /**
     * Get table name of SEO data table for ActiveRecord
     * @param ActiveRecord $activeRecord
     * @return string
     */
    public static function tableName(ActiveRecord $activeRecord)
    {
        $tableName = $activeRecord->tableName();
        if (substr($tableName, -2) == '}}') {
            return preg_replace('/{{(.+)}}/', '{{$1_' . self::$tableSuffix . '}}', $tableName);
        } else {
            return $tableName . '_' . self::$tableSuffix;
        }
    }

    /**
     * Find all SEO data.
     * Result will be in format ['condition1'=>Seo(), 'condition2'=>Seo(), ...]
     * @param ActiveRecord $owner the model for which to find data
     * @return self[]
     */
    public static function findAll(ActiveRecord $owner)
    {
        /** @var self[] $seoList */
        $seoList = [];
        if ($owner->getIsNewRecord()) {
            return $seoList;
        }
        $query = (new Query())->from(Seo::tableName($owner))->andWhere(['model_id' => $owner->getPrimaryKey()]);
        foreach ($query->all($owner->getDb()) as $seo) {
            $seo = new Seo($seo);
            $seo->_isNewRecord = false;
            $seo->_owner = $owner;
            $seoList[$seo['condition']] = $seo;
        }
        return $seoList;
    }

    /**
     * Delete all SEO data for ActiveRecord
     * @param ActiveRecord $owner
     */
    public static function deleteAll(ActiveRecord $owner)
    {
        $owner->getDb()->createCommand()->delete(self::tableName($owner), ['model_id' => $owner->getPrimaryKey()])->execute();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'keywords', 'description'], 'safe'],
        ];
    }

    /**
     * Save SEO data
     */
    public function save()
    {
        if (empty($this->_owner)) {
            $this->addError('owner', "You can't use Seo model without owner");
            return false;
        }
        $this->model_id = $this->_owner->getPrimaryKey();
        $command = $this->_owner->getDb()->createCommand();
        $attributes = ['title', 'keywords', 'description'];
        $tableName = self::tableName($this->_owner);
        if ($this->_isNewRecord) {
            if (empty(array_filter($this->getAttributes($attributes)))) {
                // don't save new SEO data with empty values
                return true;
            }
            $command->insert($tableName, $this->toArray());
        } else {
            $command->update(
                $tableName, $this->toArray($attributes),
                ['model_id' => $this->model_id, 'condition' => $this->condition]
            );
        }
        $command->execute();
        $this->_isNewRecord = false;
        return true;
    }

    /**
     * Delete SEO data
     * @return $this|bool
     */
    public function delete()
    {
        if (!$this->_isNewRecord) {
            $this->_owner->getDb()
                ->createCommand()
                ->delete(
                    self::tableName($this->_owner),
                    ['model_id' => $this->_owner->getPrimaryKey(), 'condition' => $this->condition]
                )->execute();
        }
    }

    /**
     * Get isNewRecord flag
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->_isNewRecord;
    }
}
