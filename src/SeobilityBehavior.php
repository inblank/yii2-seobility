<?php
/**
 * The SEO support behavior for ActiveRecords in Yii 2 framework
 *
 * @link https://github.com/inblank/yii2-seobility
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */

namespace inblank\seobility;

use inblank\seobility\models\Seo;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * The SEO support behavior for ActiveRecords in Yii 2 framework
 *
 * @property ActiveRecord $owner
 *
 * @author Pavel Aleksandrov <inblank@yandex.ru>
 */
class SeobilityBehavior extends Behavior
{
    /**
     * @var Seo[] seo data cache for owner
     */
    public $_seo = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * Get all SEO data for model
     * @param bool $force if `true`, force get with overwrite current SEO data
     * @return array
     */
    public function getAllSeobility($force = false)
    {
        $this->_seo = (array)($force ? [] : $this->_seo) + Seo::findAll($this->owner);
        return ArrayHelper::toArray($this->_seo, ['inblank\seobility\models\Seo' => ['title', 'keywords', 'description']]);
    }

    /**
     * Set SEO data
     * @param string[] $values setting values
     * @param int $condition SEO data condition for select. Condition =0 for default SEO data
     */
    public function setSeobility($values, $condition = 0)
    {
        $condition = (int)$condition;
        if (!array_key_exists($condition, $this->_seo)) {
            $this->_seo[$condition] = Seo::find($this->owner, $condition);
        }
        $this->_seo[$condition]->setAttributes($values);
    }

    /**
     * After save event
     */
    public function afterSave()
    {
        foreach ($this->_seo as $i => $seo) {
            $seo->save();
        }
    }

    /**
     * After delete event
     */
    public function afterDelete()
    {
        Seo::deleteAll($this->owner);
        $this->_seo = [];
    }

    /**
     * Delete SEO data
     * @param int $condition delete SEO data condition
     */
    public function deleteSeobility($condition = 0)
    {
        $condition = (int)$condition;
        $this->getSeobility($condition, false);
        $this->_seo[$condition]->delete();
        unset($this->_seo[$condition]);
    }

    /**
     * Get SEO data
     * @param int $condition SEO data condition for get. Default SEO data have condition=0
     * @param bool $defaultIfNotFound flag that get default SEO data if not found by condition
     * @param int $defaultCondition default SEO data condition for get.
     * If can't get SEO data with this condition will be return empty SEO data
     * @return \string[]
     */
    public function getSeobility($condition = 0, $defaultIfNotFound = true, $defaultCondition = 0)
    {
        $condition = (int)$condition;
        if (!array_key_exists($condition, $this->_seo)) {
            $this->_seo[$condition] = Seo::find($this->owner, $condition);
            if ($this->_seo[$condition]->getIsNewRecord() && $defaultIfNotFound) {
                $this->getSeobility((int)$defaultCondition, false);
            }
        }
        return $this->_seo[$condition]->getAttributes(['title', 'keywords', 'description']);
    }

    /**
     * Delete all SEO data
     */
    public function deleteAllSeobility()
    {
        Seo::deleteAll($this->owner);
    }
}
