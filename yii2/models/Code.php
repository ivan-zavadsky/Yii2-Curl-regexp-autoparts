<?php

namespace app\models;

use yii\base\Model;

class Code extends Model
{
    public ?string $name = null;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
        ];
    }
}
