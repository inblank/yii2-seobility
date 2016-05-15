<?php

namespace inblank\taggable\tests;

use app\models\Model;
use app\models\Model2;
use Codeception\Specify;
use inblank\seobility\models\Seo;
use yii;
use yii\codeception\TestCase;
use yii\db\Query;

class SeobilityTest extends TestCase
{
    use Specify;

    /**
     * General test for Page model
     */
    public function testGeneral()
    {
        $values = [
            'name' => 'Test model 1',
        ];
        $seo = [
            'title' => 'SEO title',
            'keywords' => 'SEO keywords',
            'description' => 'SEO description',
        ];
        $seo2 = [
            'title' => 'SEO title 2',
            'keywords' => 'SEO keywords 2',
            'description' => 'SEO description 2',
        ];

        $this->specify("we check validation for model SEO", function () {
            $seo = new Seo;
            expect("we can't save seo with empty owner `model_id` field", $seo->save())->false();
            expect("error must contain `owner` field", $seo->getErrors())->hasKey('owner');
        });

        $this->specify("we test work with new model without SEO", function () use ($values) {
            /** @var Model $model */
            $model = new Model($values);
            $checkValue = ['title' => '', 'keywords' => '', 'description' => ''];
            expect("we gave empty all SEO data", count($model->getAllSeobility()))->equals(0);
            expect("we can get empty SEO data", $model->getSeobility())->equals($checkValue);
            expect("we can save model without SEO data", $model->save())->true();
        });

        $this->specify("we work with fetched model with empty SEO data", function () use ($values, $seo) {
            $model = Model::findOne(1);
            expect("model must be in database", $model)->notNull();
            expect("model must have saved name", $model->name)->equals($values['name']);
            $checkValue = ['title' => '', 'keywords' => '', 'description' => ''];
            expect("model must have empty SEO data", $model->getSeobility())->equals($checkValue);

            $model->setSeobility(['title' => $seo['title']]);
            $checkValue['title'] = $seo['title'];
            expect("we set SEO data for model and must read it", $model->getSeobility())->equals($checkValue);
            $model->save();
            expect("model must have one SEO data record", count($model->getAllSeobility(true)))->equals(1);
            expect("saved SEO data record must be equals", $model->getSeobility())->equals($checkValue);
        });

        $this->specify("we work with fetched model with SEO data", function () use ($values, $seo) {
            $model = Model::findOne(1);
            $checkValue = ['title' => $seo['title'], 'keywords' => '', 'description' => ''];
            expect("model must have not empty SEO data", $model->getSeobility())->equals($checkValue);

            $model->setSeobility(['keywords' => $seo['keywords'], 'description' => $seo['description']]);
            $checkValue['keywords'] = $seo['keywords'];
            $checkValue['description'] = $seo['description'];
            expect("we added keywords and must see it", $model->getSeobility())->equals($checkValue);
            $model->save();
            expect("model must have one SEO data record", count($model->getAllSeobility(true)))->equals(1);
            expect("saved SEO data record must be equals", $model->getSeobility())->equals($checkValue);
        });

        $this->specify("we add new conditional SEO data to model ", function () use ($values, $seo, $seo2) {
            $model = Model::findOne(1);
            expect("model must have one SEO data record", count($model->getAllSeobility(true)))->equals(1);
            expect("model must have not empty SEO data", $model->getSeobility())->equals($seo);
            $model->setSeobility($seo2, 1);
            expect("we added new conditional SEO data", $model->getSeobility(1))->equals($seo2);
            $model->save();
            expect("model must have two SEO data record", count($model->getAllSeobility(true)))->equals(2);
            expect("saved SEO data record 1 must be equals", $model->getSeobility())->equals($seo);
            expect("saved SEO data record 2 must be equals", $model->getSeobility(1))->equals($seo2);
        });

        $this->specify("test direct access to default SEO data", function () use ($values, $seo) {
            /** @var Model $model */
            $model = Model::findOne(1);
            expect("SEO data must be equal", $model->seobility)->equals($seo);
            $newTitle = ['title' => 'Updated title by direct access'];
            $model->seobility = $newTitle;
            expect("SEO title must be new", $model->seobility['title'])->equals($newTitle['title']);
        });

        $this->specify("we delete one SEO data", function () use ($values, $seo2) {
            $model = Model::findOne(1);
            $model->deleteSeobility();
            expect("SEO data must be empty", array_filter($model->getSeobility()))->isEmpty();
            $seobility = $model->getAllSeobility(true);
            expect("model must have one SEO data record", count($seobility))->equals(1);
            expect("SEO data must be equal", $seobility[1])->equals($seo2);
        });

        $this->specify("we delete all SEO data", function () use ($values, $seo2) {
            $model = Model::findOne(1);
            $model->deleteAllSeobility();
            expect("all SEO data must be empty", array_filter($model->getAllSeobility()))->isEmpty();
        });

        $this->specify("we create new model with SEO data and delete them", function () use ($values, $seo2) {
            $model = new Model([
                'name' => 'Model 2'
            ]);
            $title = 'SEO title';
            $title2 = 'SEO title 2';
            $model->setSeobility(['title' => $title]);
            $model->setSeobility(['title' => $title2], 1);
            expect("model must saved", $model->save())->true();
            $modelId = $model->id;
            $tableName = Seo::tableName($model);
            expect("model must have two SEO data records", count($model->getAllSeobility()))->equals(2);
            $model->delete();
            expect("model not found", Model::findOne($modelId))->null();
            expect("SEO data for model not found", (new Query())->from($tableName)->andWhere(['model_id' => $modelId])->count('*', Model::getDb()))
                ->equals(0);
        });

        $this->specify("check work with ActiveRecord table name with {{%}}", function () use ($values, $seo2) {
            $model = new Model2([
                'name' => 'Other model'
            ]);
            $title = 'Other model SEO title';
            $title2 = 'Other model SEO title 2';
            $model->setSeobility(['title' => $title]);
            $model->setSeobility(['title' => $title2], 1);
            expect("model must saved", $model->save())->true();
            expect("model must have two SEO data records", count($model->getAllSeobility()))->equals(2);
        });

    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
