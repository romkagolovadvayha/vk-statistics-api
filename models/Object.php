<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Object extends ActiveRecord
{
    public static function tableName()
    {
        return 'objects';
    }

    public function getUserToObjects()
    {
        return $this->hasMany(UserToObjects::className(), ['objectId' => 'id']);
    }

    /**
     * Публичный массив модели
     */
    public function publicArray($object = NULL)
    {
        $object = $object ?? $this;
        return ArrayHelper::toArray($object, [
            'app\models\Object' => [
                'object_id',
                'object_type',
                'name',
                'photo_50',
                'is_closed',
                'is_closed',
                'members_count',
                'create',
            ],
        ]);
    }
}