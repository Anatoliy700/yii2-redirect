<?php


namespace anatoliy700\redirect;


class Configurable extends \krok\configure\Configurable
{
    /**
     * @var string
     */
    public $errorAction = 'redirect/default/error';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['errorAction'], 'string'],
        ];
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return 'Модуль редиректа';
    }

    /**
     * @return array
     */
    public static function attributeTypes(): array
    {
        return [
            'errorAction' => 'Экшен, которому будет передано управление в случае если редирект разрешить не удалось',
        ];
    }
}
