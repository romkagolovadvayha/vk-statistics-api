<?php

namespace app\controllers;

use app\models\Group;
use app\models\Object;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

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
     *     name="access_token", required=true, in="query", description="Token authorized", default="ddEiq0BZ0Hi-OU8S3xVFFFF70it7tzNs",
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
     *   path="/api/objects/add", tags={"objects"},
     *   summary="Добавление обьекта текущему пользователя", description="",
     *   @SWG\Parameter(
     *     name="vk_object_id", required=true, in="query", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="vk_object_type", required=true, in="query", description="Тип обьекта вконтакте (user, group, event, public)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized", default="ddEiq0BZ0Hi-OU8S3xVFFFF70it7tzNs",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Добавление обьекта текущему пользователя")
     * )
     */
    public function actionAdd()
    {
        if (!in_array(\Yii::$app->request->get('vk_object_type'), ['user', 'group', 'event', 'public'])) {
            return ['status' => 1, 'object' => 'Тип обьекта не поддерживается!'];
        }
        $model = new \app\models\Object();
        $object = ArrayHelper::toArray($model, [
            'app\models\Object' => [
                'id',
                'name',
            ],
        ]);
        return ['status' => 1, 'object' => $object];
    }

    /**
     * @SWG\Delete(
     *   path="/api/objects/delete", tags={"objects"},
     *   summary="Удаление обьекта у текущего пользователя", description="",
     *   @SWG\Parameter(
     *     name="vk_object_id", required=true, in="query", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="vk_object_type", required=true, in="query", description="Тип обьекта вконтакте (user, group, event)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized", default="ddEiq0BZ0Hi-OU8S3xVFFFF70it7tzNs",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Удаление обьекта у текущего пользователя")
     * )
     */
    public function actionDelete()
    {
        $model = new \app\models\Object();
        $model->load(\Yii::$app->request->get(), '');

        if (!$model->validate()) {
            return ['status' => 0, 'errors' => $model->getErrors()];
        }

        return ['status' => 1];
    }
}
