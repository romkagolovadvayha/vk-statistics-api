<?php

namespace app\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\web\UnauthorizedHttpException;

class User extends ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($user_id)
    {
        return static::findOne(['user_id' => $user_id]);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Создать нового пользователя
     */
    public static function create($params)
    {
        $model = new User();
        $model->user_id = $params['user_id'] ?? NULL;
        $model->balance = $params['balance'] ?? 0;
        $model->photo_50 = $params['photo_50'] ?? NULL;
        $model->name = $params['name'] ?? NULL;
        $model->access_token = $params['access_token'] ?? NULL;
        return $model->insert();
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->access_token;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Публичный массив модели
     */
    public function publicArray()
    {
        return ArrayHelper::toArray($this, [
            'app\models\User' => [
                'user_id',
                'name',
                'balance',
                'photo_50',
                'access_token',
            ],
        ]);
    }

    /**
     * Проверка подписи
     */
    public function checkSign()
    {
        //$url = $_SERVER['HTTP_REFERER'];
        $url = '?vk_access_token_settings=groups%2Cstats&vk_app_id=7178535&vk_are_notifications_enabled=0&vk_is_app_user=1&vk_is_favorite=0&vk_language=ru&vk_platform=desktop_web&vk_ref=other&vk_user_id=33610634&sign=GxrUQiTUqw0D9_9LUO3Wro8Gnjdsr4NQx5c42nnCZ9M';

        $query_params = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $query_params); // Получаем query-параметры из URL

        $sign_params = [];
        foreach ($query_params as $name => $value) {
            if (strpos($name, 'vk_') !== 0) { // Получаем только vk параметры из query
                continue;
            }
            $sign_params[$name] = $value;
        }

        ksort($sign_params); // Сортируем массив по ключам
        $sign_params_query = http_build_query($sign_params); // Формируем строку вида "param_name1=value&param_name2=value"
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, \Yii::$app->params['vk']['client_secret'], true)), '+/', '-_'), '='); // Получаем хеш-код от строки, используя защищеный ключ приложения. Генерация на основе метода HMAC.

        // Сравниваем полученную подпись со значением параметра 'sign'
        if ($sign !== $query_params['sign']) {
            throw new UnauthorizedHttpException('Ошибка авторизации!', 403);
        }
        return true;
    }
}
