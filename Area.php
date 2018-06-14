<?php

namespace lonelythinker\yii2\area;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Area extends Widget
{
    public $model = null;

    /**
     * @var string 此属性不用处理
     */
    public $attribute;

    /**
     * @var array 省份配置
     */
    public $province = [];

    /**
     * @var array 城市配置
     */
    public $city = [];

    /**
     * @var array 县区配置
     */
    public $county = [];

    /**
     * @var mixed 数据源
     */
    public $url;

    public function init()
    {
        if (!$this->model) {
            throw new InvalidParamException('model不能为null!');
        }

        if (empty($this->province) || empty($this->city)) {
            throw new InvalidParamException('province和city不能为空！');
        }

        $joinChar = strripos($this->url, '?') ? '&' : '?';
        $url = $this->url . $joinChar;

        if (!empty($this->county)) {
            if (empty($this->county['options']['prompt'])) {
                $this->county['options']['prompt'] = '选择县区';
            }
            $countyId = Html::getInputId($this->model, $this->county['attribute']);
            $countyDefault = Html::renderSelectOptions('county', ['' => $this->county['options']['prompt']]);
            $this->city['options'] = ArrayHelper::merge($this->city['options'], [
                'onchange' => "
                    if($(this).val() != ''){
                        $.get('{$url}parent_id='+$(this).val(), function(data) {
                            $('#{$countyId}').html('{$countyDefault}'+data);
                        })
                    }else{
                        $('#{$countyId}').html('{$countyDefault}');
                    }
                "
            ]);
        }

        if (!empty($this->city)) {
            if (empty($this->city['options']['prompt'])) {
                $this->city['options']['prompt'] = '选择城市';
            }
            $cityId = Html::getInputId($this->model, $this->city['attribute']);
            $cityDefault = Html::renderSelectOptions('city', ['' => $this->city['options']['prompt']]);
            if (!empty($this->county)) {
                $this->province['options'] = ArrayHelper::merge($this->province['options'], [
                    'onchange' => "
                if($(this).val()!=''){
                    $.get('{$url}parent_id='+$(this).val(), function(data) {
                        $('#{$cityId}').html('{$cityDefault}'+data);
                    })
                }else{
                    $('#{$cityId}').html('{$cityDefault}');
                }
                $('#{$countyId}').html('{$countyDefault}');
            "
                ]);
            } else {
                $this->province['options'] = ArrayHelper::merge($this->province['options'], [
                    'onchange' => "
                if($(this).val()!=''){
                    $.get('{$url}parent_id='+$(this).val(), function(data) {
                        $('#{$cityId}').html('{$cityDefault}'+data);
                    })
                }else{
                    $('#{$cityId}').html('{$cityDefault}');
                }
            "
                ]);
            }
        }
    }

    public function run()
    {
        $output[] = Html::activeDropDownList($this->model, $this->province['attribute'], $this->province['items'],
            $this->province['options']);
        if (!empty($this->city)) {
            $output[] = Html::activeDropDownList($this->model, $this->city['attribute'], $this->city['items'],
                $this->city['options']);
        }
        if (!empty($this->county)) {
            $output[] = Html::activeDropDownList($this->model, $this->county['attribute'], $this->county['items'],
                $this->county['options']);
        }
        return @implode("\n", $output);
    }

}
