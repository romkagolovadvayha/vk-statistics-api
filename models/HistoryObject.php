<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class HistoryObject extends ActiveRecord
{
    public static function tableName()
    {
        return 'history_object';
    }

    public function getObject()
    {
        return $this->hasOne(Object::className(), ['id' => 'objectId']);
    }

    /**
     * Создать обьект
     */
    public static function create($params)
    {
        $model = new HistoryObject();
        $model->objectId = $params['objectId'] ?? NULL;
        $model->members_count = $params['members_count'] ?? 0;
        $model->members_ids = $params['members_ids'] ?? NULL;
        $model->members_in_ids = $params['members_in_ids'] ?? '';
        $model->members_out_ids = $params['members_out_ids'] ?? '';
        return $model->insert();
    }

    public static function findByObjectId($objectId)
    {
        return static::findAll(['objectId' => $objectId]);
    }

    /**
     * Публичный массив модели
     */
    public function publicArray($object = NULL)
    {
        $object = $object ?? $this;
        return ArrayHelper::toArray($object, [
            'app\models\HistoryObject' => [
                'members_count',
                'members_ids',
                'members_in_ids',
                'members_out_ids',
                'create',
            ],
        ]);
    }
}