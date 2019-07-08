<?php


namespace anatoliy700\redirect\actions;

use anatoliy700\redirect\Configurable;
use anatoliy700\redirect\exceptions\RedirectItemNotFoundException;
use anatoliy700\redirect\IRedirect;
use krok\configure\ConfigurableNotFoundException;
use krok\configure\ConfigureInterface;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RedirectAction extends Action
{
    /**
     * @var IRedirect
     */
    protected $redirect;

    /** @var Configurable */
    protected $configurable;

    /**
     * RedirectAction constructor.
     * @param $id
     * @param $controller
     * @param IRedirect $redirect
     * @param ConfigureInterface $configure
     * @param array $config
     * @throws ConfigurableNotFoundException
     */
    public function __construct($id, $controller, IRedirect $redirect, ConfigureInterface $configure, $config = [])
    {
        $this->redirect = $redirect;
        $this->configurable = $configure->get(Configurable::class);
        parent::__construct($id, $controller, $config);
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        try {
            $redirectItem = $this->redirect->getRedirectItem(Yii::$app->request);

            return Yii::$app->getResponse()->redirect([$redirectItem->getNewPath()], $redirectItem->getStatusCode());
        } catch (RedirectItemNotFoundException $e) {
            Yii::$app->errorHandler->errorAction = $this->configurable->errorAction;
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            Yii::$app->errorHandler->handleException($exception);
        }
    }
}
