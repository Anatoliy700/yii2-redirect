<?php


namespace anatoliy700\redirect\models;


interface IRedirectItem
{
    /**
     * @return string
     */
    public function getOldPath(): string;

    /**
     * @return string
     */
    public function getNewPath(): string;

    /**
     * @return string
     */
    public function getStatusCode(): string;
}
