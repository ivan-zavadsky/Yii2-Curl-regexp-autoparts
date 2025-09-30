<?php

namespace app\models;

use app\entities\Page;
use yii\base\Model;

class Curler extends Model
{
    public $input;

    public function rules(): array
    {
        return [
            [['input'], 'required'],
        ];
    }

    public function process()
    {
        $page = new Page($this->input);
        $page->getRawPage();
        if (
            $page->hasProductsData()
        ) {
            $page->extract();
            $page->save();
        }

    }

}
