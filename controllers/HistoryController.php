<?php

namespace app\controllers;

use app\models\Request\ObjectHistoryAddRequest;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

/**
 * @SWG\Tag(
 *   name="history",
 *   description="История обьектов"
 * )
 */
class HistoryController extends Controller
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
     * @SWG\Post(
     *   path="/api/histories/{object_type}/{object_id}/add", tags={"history"},
     *   summary="Добавление истории обьекта", description="",
     *   consumes={"application/json"},
     *   produces={"application/xml", "application/json"},
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
     *   @SWG\Parameter(
     *     name="body", required=true, in="body",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="members_count",
     *              description="Количество участников, подписчиков",
     *              required=true,
     *              type="integer",
     *              format="int64"
     *          ),
     *          @SWG\Property(
     *              property="members_ids",
     *              required=true,
     *              description="Список через запятую ID участников",
     *              type="string"
     *          ),
     *          example={"members_count": 2, "members_ids": "3412344,4125555"}
     *     )
     *   ),
     *   @SWG\Response(response=200, description="Добавление истории обьекта")
     * )
     */
    public function actionAdd()
    {
        $model = new ObjectHistoryAddRequest();
        $model->load(\Yii::$app->request->get(), '');
        $model->load(\Yii::$app->request->post(), '');

        if (!$model->validate()) {
            throw new ServerErrorHttpException(array_values($model->getFirstErrors())[0]);
        }
        $object = \app\models\Object::findByIdAndType($model->object_id, $model->object_type);
        if (empty($object)) {
            throw new ServerErrorHttpException('Не найден!');
        }

        \app\models\HistoryObject::create([
            'objectId' => $object->id,
            'members_count' => $model->members_count,
            'members_ids' => $model->members_ids
        ]);

        return ['status' => 1];
    }
}
