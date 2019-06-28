<?php


namespace anatoliy700\redirect;


use anatoliy700\redirect\repositories\IRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Module extends \yii\base\Module
{

    /**
     * @var IRepository
     */
    public $urlRepository;

    /**
     * @var string
     */
    public $errorAction;

    /**
     * @var bool
     */
    public $isForwardQueryParams = false;

    /**
     * Module constructor.
     * @param $id
     * @param null $parent
     * @param array $config
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function __construct($id, $parent = null, $config = [])
    {
        if (isset($config['urlRepository'])) {
            $this->setUrlRepository($config['urlRepository']);
            unset($config['urlRepository']);
        }

        parent::__construct($id, $parent, $config);
    }


    /**
     * @throws \yii\console\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function init()
    {
        parent::init();

        $this->doRedirect(\Yii::$app->request->resolve());
    }

    /**
     * @param $config
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    protected function setUrlRepository($config)
    {
        if (is_array($config) && isset($config['class'])) {
            $class = $config['class'];
        } elseif (is_string($config)) {
            $class = $config;
        } else {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        }

        if (!(new \ReflectionClass($class))->implementsInterface(IRepository::class)) {
            throw new InvalidConfigException(
                "The class {$class} passed does not implement the interface ".IRepository::class.'.'
            );
        }

        $this->urlRepository = \Yii::createObject($config);
    }

    /**
     * @param array $requestParams
     */
    public function doRedirect(array $requestParams)
    {
        list($pathInfo, $queryParams) = $requestParams;

        $redirectItem = $this->urlRepository->getRedirectItemByOldPath($pathInfo);

        if (is_null($redirectItem)) {
            if (isset($this->errorAction)) { //TODO: Сделать дефолтный роут
                \Yii::$app->errorHandler->errorAction = $this->errorAction;
            } else {
                if (\Yii::$app->errorHandler->errorAction === $this->id) {
                    \Yii::$app->errorHandler->errorAction = null;
                }
            }
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            Yii::$app->errorHandler->handleException($exception);
        }

        $url = [$redirectItem->newPath];

        if ($this->isForwardQueryParams) {
            $url = ArrayHelper::merge($url, $queryParams);
        }

        \Yii::$app->response->redirect($url, $redirectItem->statusCode ?? '')->send();
    }
}
