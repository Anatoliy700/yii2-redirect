<?php


namespace anatoliy700\redirect;


use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\IRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\NotFoundHttpException;

class Module extends \yii\base\Module
{

    /**
     * @var IRepository
     */
    public $urlRepository;

    /**
     * Можно указать Action, который будет вызван,
     * в случае если модуль работает в постзагрузочном режиме
     * и не было найдено ни одного подходящего иаршрута для редиректа.
     *
     * @var string
     */
    public $errorAction;

    /**
     * Позволяет указать необходимость включения queryParams,
     * если они есть, в маршрут, на который производится редирект
     *
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
     * @throws NotFoundHttpException
     * @throws \yii\console\Exception
     */
    public function init()
    {
        parent::init();

        if (Yii::$app->state === Application::STATE_INIT) {
            Yii::$app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'eventHandler']);
        } else {
            $this->run(false);
        }
    }

    /**
     * @param $event
     * @throws NotFoundHttpException
     * @throws \yii\console\Exception
     */
    public function eventHandler($event)
    {
        $this->run();
    }

    /**
     * Защита от ошибки на случай если в конфигурации установлено
     * оба варианта работы модуля (предзагрузка и постзагрузка).
     *
     * @param string $route
     * @return array|bool|void
     */
    public function createController($route)
    {
        $this->notFoundPageSend();
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
     * @param bool $beforeRequest
     * @throws NotFoundHttpException
     * @throws \yii\console\Exception
     */
    public function run(bool $beforeRequest = true)
    {
        list($pathInfo, $queryParams) = \Yii::$app->request->resolve();
        $redirectItem = $this->urlRepository->getRedirectItemByOldPath($pathInfo);
        if (isset($redirectItem)) {
            $this->doRedirect($redirectItem, $queryParams);
        } elseif (!$beforeRequest) {
            $this->notFoundPageSend();
        }
    }

    /**
     * @param IRedirectItem|null $redirectItem
     * @param $queryParams
     */
    public function doRedirect(?IRedirectItem $redirectItem, $queryParams)
    {
        $url = [$redirectItem->getNewPath()];

        if ($this->isForwardQueryParams) {
            $url = ArrayHelper::merge($url, $queryParams);
        }

        \Yii::$app->response->redirect($url, $redirectItem->getStatusCode())->send();
        exit();
    }

    /**
     *
     */
    public function notFoundPageSend()
    {
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
}
