<?php


namespace anatoliy700\redirect;


use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\IRepository;

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
     * @param string $pathInfo
     * @param array $queryParams
     * @return IRedirectItem
     */
    public function getRedirectItem(string $pathInfo, array $queryParams): ?IRedirectItem
    {
        return $this->repository->getRedirectItemByOldPath($pathInfo);
    }
}
