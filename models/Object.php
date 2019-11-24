<?php

namespace app\models;

use VK\Client\VKApiClient;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

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

    public function getHistory()
    {
        return $this->hasMany(HistoryObject::className(), ['objectId' => 'id']);
    }

    /**
     * Создать обьект
     */
    public static function create($params)
    {
        $model = new Object();
        $model->object_id = $params['object_id'] ?? NULL;
        $model->object_type = $params['object_type'] ?? NULL;
        $model->name = $params['name'] ?? NULL;
        $model->photo_50 = $params['photo_50'] ?? NULL;
        $model->is_closed = $params['is_closed'] ?? 0;
        $model->members_count = $params['members_count'] ?? 0;
        return $model->insert();
    }

    public static function findByIdAndType($object_id, $object_type)
    {
        return static::findOne(['object_id' => $object_id, 'object_type' => $object_type]);
    }

    /*
     * @retur boolean
     */
    public static function createGroup($object_id, $object_type): bool
    {
        if (!in_array($object_type, ['group', 'page'])) {
            return false;
        }
        $vk = new VKApiClient(\Yii::$app->params['vk']['version']);
        try {
            $group = $vk->groups()->getById(\Yii::$app->params['vk']['access_token'], [
                'group_id' => $object_id,
                'fields' => 'members_count',
            ]);
        } catch (VKApiException $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
        if ($group[0]['is_closed'] !== 0) {
            throw new ServerErrorHttpException('Сообщество закрыто!');
        }
        \app\models\Object::create([
            'object_id' => $object_id,
            'object_type' => $object_type,
            'name' => $group[0]['name'],
            'photo_50' => $group[0]['photo_50'],
            'members_count' => $group[0]['members_count'],
        ]);
        return true;
    }
    /*
     * @retur boolean
     */
    public static function createUser($object_id, $object_type): bool
    {
        if (!in_array($object_type, ['user'])) {
            return false;
        }
        $vk = new VKApiClient(\Yii::$app->params['vk']['version']);
        try {
            $user = $vk->users()->get(\Yii::$app->params['vk']['access_token'], [
                'user_ids' => [$object_id],
                'fields' => 'photo_50, counters',
            ]);
        } catch (VKApiException $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
        if ($user[0]['is_closed']) {
            throw new ServerErrorHttpException('Страница закрыта!');
        }
        \app\models\Object::create([
            'object_id' => $object_id,
            'object_type' => $object_type,
            'name' => $user[0]['first_name'] . ' ' . $user[0]['last_name'],
            'photo_50' => $user[0]['photo_50'],
            'members_count' => $user[0]['counters']['friends'],
        ]);
        return true;
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