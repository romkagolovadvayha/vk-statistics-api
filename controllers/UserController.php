<?php

namespace app\controllers;

use app\models\Request\UserAuthRequest;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
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
        $model = new UserAuthRequest();
        $model->load(\Yii::$app->request->get(), '');
        if (!$model->validate()) {
            throw new ServerErrorHttpException(array_values($model->getFirstErrors())[0]);
        }

        $access_token = \Yii::$app->security->generateRandomString();

        $user = \app\models\User::findIdentity($model->user_id);
        if (empty($user)) {
            // если пользователь еще не создан создать
            $vk = new VKApiClient(\Yii::$app->params['vk']['version']);
            try {
                $VKUser = $vk->users()->get(
                    \Yii::$app->params['vk']['access_token'],
                    ['user_ids' => [$model->user_id], 'fields' => 'photo_50']
                );
            } catch (VKApiException $e) {
                throw new ServerErrorHttpException($e->getMessage());
            }
            if (empty($VKUser) || !empty($VKUser[0]['deactivated'])) {
                throw new ServerErrorHttpException('Пользователь не существует!');
            }
            \app\models\User::create([
                'user_id' => $model->user_id,
                'name' => $VKUser[0]['first_name'] . ' ' . $VKUser[0]['last_name'],
                'photo_50' => $VKUser[0]['photo_50'],
                'access_token' => $access_token,
            ]);
            $user = \app\models\User::findIdentity($model->user_id);
        } else {
            $user->access_token = $access_token;
            $user->save();
        }

        return ['status' => 1, 'user' => $user->publicArray()];
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
     *     name="access_token", required=true, in="query", description="Token authorized",
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
