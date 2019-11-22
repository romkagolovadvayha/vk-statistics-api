<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/x-www-form-urlencoded"},
 *     @SWG\Info(version="1.0", title="API статистика вконтакте"),
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

    /**
     * @SWG\Get(
     *   path="/api/objects/add", tags={"objects"},
     *   summary="Добавление обьекта текущему пользователя", description="",
     *   @SWG\Parameter(
     *     name="vkObjectId", required=true, in="query", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="vkObjectType", required=true, in="query", description="Тип обьекта вконтакте (user, group, event)",
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
        try {
            $model = new \app\models\Object();
            $model->load(\Yii::$app->request->get(), '');
            if (!$model->validate()) {
                return ['status' => 0, 'errors' => $model->getErrors()];
            }
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }

        return ['status' => 1, 'action' => 'actionAdd', 'object' => $model->toArray()];
    }

    /**
     * @SWG\Delete(
     *   path="/api/objects/delete", tags={"objects"},
     *   summary="Удаление обьекта у текущего пользователя", description="",
     *   @SWG\Parameter(
     *     name="vkObjectId", required=true, in="query", description="ID обьекта вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="vkObjectType", required=true, in="query", description="Тип обьекта вконтакте (user, group, event)",
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

        return ['status' => 1, 'action' => 'actionDelete', 'object' => $model->toArray()];
    }
}
