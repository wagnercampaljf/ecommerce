<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_faq".
 *
 * @property integer $id
 * @property string $titulo
 * @property string $descricao
 * @property integer $categoria_id
 *
 * @property CategoriaFaq $categoria
 */
class ItemFaq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_faq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['titulo', 'descricao', 'categoria_id'], 'required'],
            [['categoria_id'], 'integer'],
            [['titulo'], 'string', 'max' => 100],
            [['descricao'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'descricao' => 'Descricao',
            'categoria_id' => 'Categoria ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(CategoriaFaq::className(), ['id' => 'categoria_id']);
    }

    public static function find()
    {
        return new ItemFaqQuery(get_called_class());
    }
}

class ItemFaqQuery extends \yii\db\ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nome' => $sort_type]);
    }

    /**
     * Filtra os itens que pertencem a dada $categoria (id)
     * @author Vinicius Schettino 02/12/2014
     */
    public function byCategoria($categoria)
    {
        return $this->andWhere(
            ['categoria_id' => $categoria]
        );
    }
}


