<?php

namespace app\models;

use yii\base\Model;

class Object extends Model
{
    public $id;
    public $name;
    public $vkObjectId;
    public $vkObjectType;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['vkObjectId', 'required', 'message' => 'Укажите vk_object_id'],
            ['vkObjectType', 'required', 'message' => 'Укажите vk_object_type'],
            ['vkObjectType', 'validateVkObjectType']
        ];
    }

    public function validateVkObjectType($attribute) {
        if (!in_array($this->$attribute, ['user', 'group', 'event'])) {
            $this->addError($attribute, 'Тип обьекта не поддерживается!');
        }
    }
}