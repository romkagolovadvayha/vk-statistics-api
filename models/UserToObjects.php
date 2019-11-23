<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserToObjects extends ActiveRecord
{

    public static function tableName()
    {
        return 'user_to_objects';
    }

    public function getObject()
    {
        return $this->hasOne(Object::className(), ['id' => 'objectId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

}