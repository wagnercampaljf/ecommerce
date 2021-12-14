<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormSearch extends Model
{
    public $categoria_modelo_id;
    public $marca_id;
    public $modelo_id;
    public $ano_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoria_modelo_id', 'marca_id', 'modelo_id', 'ano_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

}
