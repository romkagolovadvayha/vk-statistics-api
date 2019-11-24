<?php

namespace app\models\Request;

use yii\base\Model;

class ObjectAddRequest extends Model
{
    public $object_id;
    public $object_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['object_id', 'required', 'message' => 'Укажите object_id'],
            ['object_type', 'required', 'message' => 'Укажите object_type'],
            ['object_type', 'validateObjectType'],
        ];
    }

    public function validateObjectType($attribute) {
        if (!in_array($this->$attribute, ['user', 'group', 'event', 'public', 'page'])) {
            $this->addError($attribute, 'Тип обьекта не поддерживается!');
        }
    }
}