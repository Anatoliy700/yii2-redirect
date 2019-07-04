<?php


namespace anatoliy700\redirect\models;

class RedirectItem implements IRedirectItem
{
    /**
     * @var string
     */
    protected $oldPath;

    /**
     * @var string
     */
    protected $newPath;

    /**
     * @var string
     */
    protected $statusCode;


    /**
     * RedirectItem constructor.
     * @param string $oldPath
     * @param string $newPath
     * @param string $statusCode
     */
    public function __construct(string $oldPath, string $newPath, string $statusCode = '301')
    {
        $this->oldPath = $this->normaliseData($oldPath);
        $this->newPath = $this->normaliseData($newPath);
        $this->statusCode = $this->normaliseData($statusCode);

    }

    /**
     * @inheritDoc
     */
    public function getOldPath(): string
    {
        return $this->oldPath;
    }

    /**
     * @inheritDoc
     */
    public function getNewPath(): string
    {
        return $this->newPath;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): string
    {
        return $this->statusCode;
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
