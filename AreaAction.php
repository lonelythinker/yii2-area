<?php

namespace lonelythinker\yii2\area;

use Yii;
use yii\base\Action;
use yii\helpers\Html;

class AreaAction extends Action
{
    public function run()
    {
        $parent_id = Yii::$app->request->get('parent_id');
        if (isset($parent_id) || $parent_id > 0) {
            return Html::renderSelectOptions(null, \yii\helpers\ArrayHelper::map((new \yii\db\Query())
                ->select(['id', 'short_name'])
                ->from('area')
                ->where(['parent_id' => $parent_id])
                ->all(), 'id', 'short_name'));
        } else {
            return [];
        }
    }
}