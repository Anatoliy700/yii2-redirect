<?php


namespace anatoliy700\redirect\actions;


use anatoliy700\redirect\Configurable;
use anatoliy700\redirect\IRedirect;
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
     * @throws \krok\configure\ConfigurableNotFoundException
     */
    public function __construct($id, $controller, IRedirect $redirect, ConfigureInterface $configure, $config = [])
    {
        $this->redirect = $redirect;
        $this->configurable = $configure->get(Configurable::class);
        parent::__construct($id, $controller, $config);
    }

    /**
     * @return Response
     * @throws \yii\console\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function run(): Response
    {
        $redirectItem = $this->redirect->getRedirectItem(...Yii::$app->request->resolve());

        if ($redirectItem) {
            return Yii::$app->getResponse()->redirect([$redirectItem->getNewPath()], $redirectItem->getStatusCode());
        }

        Yii::$app->errorHandler->errorAction = $this->configurable->errorAction;
        $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        Yii::$app->errorHandler->handleException($exception);
    }


}
