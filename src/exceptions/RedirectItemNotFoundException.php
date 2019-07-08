<?php

namespace anatoliy700\redirect\exceptions;

use yii\web\NotFoundHttpException;

class RedirectItemNotFoundException extends NotFoundHttpException
{
    /**
     * @var string
     */
    protected $oldPath;

    /**
     * RedirectItemNotFoundException constructor.
     * @param string $oldPath
     * @param int $code
     * @param NotFoundHttpException|null $previous
     */
    public function __construct(string $oldPath, $code = 0, NotFoundHttpException $previous = null)
    {
        $this->oldPath = $oldPath;

        parent::__construct(
            "The item redirect for this path '{$this->getOldPath()}' was not found.",
            $code,
            $previous
        );
    }

    /**
     * @return string
     */
    public function getOldPath()
    {
        return $this->oldPath;
    }
}
