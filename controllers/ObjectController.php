<?php

namespace app\controllers;

use app\models\HistoryObject;
use app\models\Object;
use app\models\Request\ObjectAddRequest;
use app\models\Request\ObjectDeleteRequest;
use app\models\Request\ObjectGetRequest;
use app\models\UserToObjects;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="API статистика вконтакте"),
 * )
 * @SWG\Tag(
 *   name="objects",
 *   description="Работа с обьектами вконтакте (user, group, event, public)"
 * )
 */
class ObjectController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = QueryParamAuth::className();
        $behaviors['authenticator']['tokenParam'] = 'access_token';
        return $behaviors;
    }

    public function beforeAction($action)
    {
        \app\models\User::checkSign();
        return parent::beforeAction($action);
    }

    /**
     * @SWG\Get(
     *   path="/api/objects", tags={"objects"},
     *   summary="Получить список обьектов текущего пользователя", description="",
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Получить список обьектов текущего пользователя")
     * )
     */
    public function actionIndex()
    {
        $object = new Object();
        return ['status' => 1, 'objects' => $object->publicArray(\Yii::$app->user->identity->objects)];
    }

    /**
     * @SWG\Get(
     *   path="/api/objects/{object_type}/{object_id}", tags={"objects"},
     *   summary="Получить список обьект", description="",
     *   @SWG\Parameter(
     *     name="object_id", required=true, in="path", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="object_type", required=true, in="path", description="Тип обьекта вконтакте (user, group, event, public)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Получить список обьект")
     * )
     */
    public function actionGet()
    {
        $model = new ObjectGetRequest();
        $model->load(\Yii::$app->request->get(), '');
        if (!$model->validate()) {
            throw new ServerErrorHttpException(array_values($model->getFirstErrors())[0]);
        }

        $object = Object::findByIdAndType($model->object_id, $model->object_type);
        if (empty($object)) {
            throw new ServerErrorHttpException('Не найден!');
        }
        $history = new HistoryObject();
        $objectResponse = $object->publicArray();
        $objectResponse['history'] = $history->publicArray($object->history);
        return ['status' => 1, 'object' => $objectResponse];
    }

    /**
     * @SWG\Get(
     *   path="/api/objects/add", tags={"objects"},
     *   summary="Добавление обьекта текущему пользователя", description="",
     *   @SWG\Parameter(
     *     name="object_id", required=true, in="query", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="object_type", required=true, in="query", description="Тип обьекта вконтакте (user, group, event, public)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Добавление обьекта текущему пользователя")
     * )
     */
    public function actionAdd()
    {
        $model = new ObjectAddRequest();
        $model->load(\Yii::$app->request->get(), '');
        if (!$model->validate()) {
            throw new ServerErrorHttpException(array_values($model->getFirstErrors())[0]);
        }

        $object = \app\models\Object::findByIdAndType($model->object_id, $model->object_type);
        if (empty($object)) {
            // если пользователь еще не создан создать
            \app\models\Object::createGroup($model->object_id, $model->object_type);
            \app\models\Object::createUser($model->object_id, $model->object_type);
            $object = \app\models\Object::findByIdAndType($model->object_id, $model->object_type);
        }
        if (!empty($object->id)) {
            \app\models\UserToObjects::create($object->id, \Yii::$app->user->identity->id);
        }

        return ['status' => 1, 'object' => $object->publicArray()];
    }

    /**
     * @SWG\Delete(
     *   path="/api/objects/{object_type}/{object_id}/delete", tags={"objects"},
     *   summary="Удаление обьекта у текущего пользователя", description="",
     *   @SWG\Parameter(
     *     name="object_id", required=true, in="path", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="object_type", required=true, in="path", description="Тип обьекта вконтакте (user, group, event, public)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Удаление обьекта у текущего пользователя")
     * )
     */
    public function actionDelete()
    {
        $model = new ObjectDeleteRequest();
        $model->load(\Yii::$app->request->get(), '');
        if (!$model->validate()) {
            throw new ServerErrorHttpException(array_values($model->getFirstErrors())[0]);
        }

        $object = Object::findOne(['object_id' => $model->object_id, 'object_type' => $model->object_type]);
        $userToObjects = UserToObjects::findOne(['objectId' => $object->id]);
        if (empty($userToObjects)) {
            throw new ServerErrorHttpException('Не найден!');
        }
        $userToObjects->delete();

        return ['status' => 1];
    }
}
