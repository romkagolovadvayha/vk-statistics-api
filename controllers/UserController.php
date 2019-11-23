<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

/**
 * @SWG\Tag(
 *   name="users",
 *   description="Работа с пользователями"
 * )
 */
class UserController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = QueryParamAuth::className();
        $behaviors['authenticator']['tokenParam'] = 'access_token';
        $behaviors['authenticator']['except'] = ['auth'];
        return $behaviors;
    }

    /**
     * @SWG\Get(
     *   path="/api/users/auth", tags={"users"},
     *   summary="Авторизация пользователя", description="",
     *   @SWG\Parameter(
     *     name="user_id", required=true, in="query", description="ID пользователя вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Parameter(
     *     name="hash", required=true, in="query", description="hash пользователя вконтакте",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Авторизация пользователя")
     * )
     */
    public function actionAuth()
    {
        try {
            $model = new \app\models\User();
            $model->load(\Yii::$app->request->get(), '');
            if (!$model->validate()) {
                return ['status' => 0, 'errors' => $model->getErrors()];
            }
        } catch (\Exception $e) {
            return ['status' => 0, 'error' => $e->getMessage()];
        }
        $user = ArrayHelper::toArray($model, [
            'app\models\User' => [
                'user_id'
            ],
        ]);
        return ['status' => 1, 'user' => $user];
    }

    /**
     * @SWG\Get(
     *   path="/api/users/bay", tags={"users"},
     *   summary="Совершение покупки текущего пользователя", description="",
     *   @SWG\Parameter(
     *     name="product", required=true, in="query", description="Название продукта покупки (Например: vip)",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Parameter(
     *     name="access_token", required=true, in="query", description="Token authorized", default="ddEiq0BZ0Hi-OU8S3xVFFFF70it7tzNs",
     *     @SWG\Schema(type="string")
     *   ),
     *   @SWG\Response(response=200, description="Совершение покупки текущего пользователя")
     * )
     */
    public function actionBay()
    {
        $model = new \app\models\User();
        $user = ArrayHelper::toArray($model, [
            'app\models\User' => [
                'user_id'
            ],
        ]);
        return ['status' => 1, 'user' => $user];
    }

}
