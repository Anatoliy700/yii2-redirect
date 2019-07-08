<?php


namespace anatoliy700\redirect\exceptions;

use yii\web\NotFoundHttpException;

/**
 * Class FileNotFoundException
 *
 * @package krok\filesystem
 */
class FileNotFoundException extends NotFoundHttpException
{
    /**
     * @var string
     */
    protected $path;

    /**
     * FileNotFoundException constructor.
     *
     * @param string $path
     * @param int $code
     * @param NotFoundHttpException|null $previous
     */
    public function __construct(string $path, int $code = 0, NotFoundHttpException $previous = null)
    {
        $this->path = $path;

        parent::__construct('File not found at path: ' . $this->getPath(), $code, $previous);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
