<?php

namespace app\controllers;

use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

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

    public function beforeAction($action)
    {
        \app\models\User::checkSign();
        return parent::beforeAction($action);
    }

    /**
     * @SWG\Get(
     *   path="/api/users/auth", tags={"users"},
     *   summary="Авторизация пользователя", description="",
     *   @SWG\Parameter(
     *     name="user_id", required=true, in="query", description="ID пользователя вконтакте",
     *     @SWG\Schema(type="integer", format="int64")
     *   ),
     *   @SWG\Response(response=200, description="Авторизация пользователя")
     * )
     */
    public function actionAuth()
    {
        $user_id = \Yii::$app->request->get('user_id');
        $model = \app\models\User::findIdentity($user_id);
        $access_token = \Yii::$app->security->generateRandomString();
        if (empty($model)) {
            // если пользователь еще не создан создать
            $vk = new VKApiClient(\Yii::$app->params['vk']['version']);
            try {
                $vkUser = $vk->users()->get(\Yii::$app->params['vk']['access_token'], [
                    'user_ids' => [\Yii::$app->request->get('user_id')],
                    'fields' => 'photo_50',
                ]);
            } catch (VKApiException $e) {
                return ['status' => 0, 'error' => $e->getMessage()];
            }
            if (empty($vkUser) || !empty($vkUser[0]['deactivated'])) {
                return ['status' => 0, 'error' => 'Пользователь не существует!'];
            }
            \app\models\User::create([
                'user_id' => \Yii::$app->request->get('user_id'),
                'name' => $vkUser[0]['first_name'] . ' ' . $vkUser[0]['last_name'],
                'photo_50' => $vkUser[0]['photo_50'],
                'access_token' => $access_token,
            ]);
            $model = \app\models\User::findIdentity($user_id);
        } else {
            $model->access_token = $access_token;
            $model->save();
        }

        return ['status' => 1, 'user' => $model->publicArray()];
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
