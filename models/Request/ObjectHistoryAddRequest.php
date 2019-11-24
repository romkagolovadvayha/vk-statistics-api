<?php

namespace app\models\Request;

use yii\base\Model;

class ObjectHistoryAddRequest extends Model
{
    public $object_id;
    public $object_type;
    public $members_count;
    public $members_ids;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['object_id', 'required', 'message' => 'Укажите object_id'],
            ['object_type', 'required', 'message' => 'Укажите object_type'],
            ['members_count', 'required', 'message' => 'Укажите members_count'],
            ['members_ids', 'required', 'message' => 'Укажите members_ids'],
        ];
    }
}