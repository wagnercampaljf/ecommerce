<?php

namespace common\models;

use yii\db\ActiveQuery;

use Yii;

/**
 * This is the model class for table "categoria_faq".
 *
 * @property integer $id
 * @property string $nome
 * @property boolean $disponibilidade_publica
 *
 * @property ItemFaq[] $itemFaqs
 */
class CategoriaFaq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categoria_faq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['disponibilidade_publica'], 'boolean'],
            [['nome'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'disponibilidade_publica' => 'Disponibilidade Publica',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItensFaq()
    {
        return $this->hasMany(ItemFaq::className(), ['categoria_id' => 'id']);
    }

    public static function find()
    {
        return new CategoriaFaqQuery(get_called_class());
    }
}

class CategoriaFaqQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nome' => $sort_type]);
    }

    public function publica($publica = true)
    {
        return $this->andWhere(
            ['disponibilidade_publica' => $publica]
        );
    }
}