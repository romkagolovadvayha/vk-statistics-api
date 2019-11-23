<?php

namespace app\models;

use yii\base\Model;

class Object extends Model
{
    public $id;
    public $name;
    public $vk_object_id;
    public $vk_object_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['vk_object_id', 'required', 'message' => 'Укажите vk_object_id'],
            ['vk_object_type', 'required', 'message' => 'Укажите vk_object_type'],
            ['vk_object_type', 'validateVkObjectType']
        ];
    }

    public function validateVkObjectType($attribute) {
        if (!in_array($this->$attribute, ['user', 'group', 'event', 'public'])) {
            $this->addError($attribute, 'Тип обьекта не поддерживается!');
        }
    }
}