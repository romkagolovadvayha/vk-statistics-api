<?php

namespace app\models\Request;

use yii\base\Model;

class UserAuthRequest extends Model
{
    public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['user_id', 'required', 'message' => 'Укажите user_id'],
        ];
    }
}