<?php


namespace anatoliy700\redirect;


use anatoliy700\redirect\exceptions\RedirectItemNotFoundException;
use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\IRepository;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class Redirect implements IRedirect
{
    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * Redirect constructor.
     * @param IRepository $repository
     */
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return IRedirectItem|null
     * @throws NotFoundHttpException
     */
    public function getRedirectItem(Request $request): ?IRedirectItem
    {
        return $this->repository->getRedirectItemByOldPath($request->resolve()[0]);
    }
}
