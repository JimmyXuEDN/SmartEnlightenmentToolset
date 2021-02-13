<?php

namespace qiniu;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use qiniu\exceptions\QiNiuException;

class QiNiuService
{
    /**
     * @var string
     */
    private $access_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var string
     */
    private $bucket_name;

    /**
     * @var string
     */
    private $image_domain;

    /**
     * @var string
     */
    private $config_prefix;

    /**
     * @var Auth
     */
    private $auth;

    public function __construct($access_key = null, $secret_key = null, $bucket_name = null, $image_domain = null, $config_prefix = 'storage_engine')
    {
        $this->config_prefix = $config_prefix;

        $this->setAccessKey($access_key, false)
            ->setSecretKey($secret_key)
            ->setBucketName($bucket_name)
            ->setImageDomain($image_domain);
    }

    /**
     * @param $filePath
     * @param string $key
     * @param null $params
     * @param null $mime
     * @param null $checkCrc
     * @return array
     * @throws \Exception
     */
    public function uploadFile($filePath, $key = '', $params = null, $mime = null, $checkCrc = null)
    {
        $key = $key ?: uniqid('uploadLocalFile_');

        $data = ['result' => false];

        $uploadManager = new UploadManager();

        try {
            list($upload_result, $upload_error) = $uploadManager->putFile($this->getUploadToken(), $key, $filePath, $params, $mime, $checkCrc);

            if (!is_null($upload_error)) {
                $data['info'] = $upload_error;
            } else {
                $data['result'] = true;
                $data['info'] = $upload_result;
                $data['url'] = "{$this->image_domain}/{$upload_result['key']}";
            }

            return $data;
        } catch (QiNiuException $exception) {
            $data['info'] = $exception->getMessage();

            return $data;
        }

    }

    /**
     * @return string
     * @throws QiNiuException
     */
    public function getUploadToken()
    {
        $token = $this->auth->uploadToken($this->bucket_name);

        if (!$token) {
            throw QiNiuException::withMessage(lang('UPLOAD_QINIU_TOKEN_ERROR'));
        }

        return $token;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getConfig($key)
    {
        return config("storage_engine.{$key}");
    }

    /**
     * @param null $accessKey
     * @param bool $refreshAuth
     * @return $this
     */
    public function setAccessKey($accessKey = null, $refreshAuth = true)
    {
        $this->access_key = $accessKey ? $accessKey : $this->getConfig('qiniu_access_key');

        $refreshAuth && $this->refreshAuthInstance();

        return $this;
    }

    /**
     * @param null $secret_key
     * @param bool $refreshAuth
     * @return $this
     */
    public function setSecretKey($secret_key = null, $refreshAuth = true)
    {
        $this->secret_key = $secret_key ? $secret_key : $this->getConfig('qiniu_secret_key');

        $refreshAuth && $this->refreshAuthInstance();

        return $this;
    }

    /**
     * @param null $bucket_name
     * @return $this
     */
    public function setBucketName($bucket_name = null)
    {
        $this->bucket_name = $bucket_name ? $bucket_name : $this->getConfig('qiniu_bucket_name');
        return $this;
    }

    /**
     * @param null $image_domain
     * @return $this
     */
    public function setImageDomain($image_domain = null)
    {
        $this->image_domain = $image_domain ? $image_domain : $this->getConfig('image_domain');
        return $this;
    }

    /**
     * @return QiNiuService
     */
    public function refreshAuthInstance()
    {
        return $this->setAuthInstance(new Auth($this->getAccessKey(), $this->getSecretKey()));
    }

    /**
     * @param Auth $auth
     * @return $this
     */
    public function setAuthInstance(Auth $auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->access_key;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }
}