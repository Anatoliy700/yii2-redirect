<?php


namespace anatoliy700\redirect\models;


use yii\base\BaseObject;

class RedirectItem extends BaseObject
{
    /**
     * @var string
     */
    private $oldPath;

    /**
     * @var string
     */
    private $newPath;

    /**
     * @var string
     */
    private $statusCode;


    /**
     * RedirectItem constructor.
     * @param string $oldPath
     * @param string $newPath
     * @param string $statusCode
     * @param array $config
     */
    public function __construct(string $oldPath, string $newPath, string $statusCode = '301', $config = [])
    {
        $this->oldPath = $oldPath;
        $this->newPath = $newPath;
        $this->statusCode = $statusCode;

        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function getOldPath(): string
    {
        return $this->oldPath;
    }

    /**
     * @return string
     */
    public function getNewPath(): string
    {
        return $this->newPath;
    }

    /**
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    /**
     * @param string $oldPath
     */
    public function setOldPath(string $oldPath): void
    {
        $this->oldPath = $this->normaliseData($oldPath);
    }

    /**
     * @param string $newPath
     */
    public function setNewPath(string $newPath): void
    {
        $this->newPath = $this->normaliseData($newPath);
    }

    /**
     * @param string $statusCode
     */
    public function setStatusCode(string $statusCode): void
    {
        $this->statusCode = $this->normaliseData($statusCode);
    }

    /**
     * @param string $data
     * @return string
     */
    protected function normaliseData(string $data): string
    {
        return trim($data);
    }
}
