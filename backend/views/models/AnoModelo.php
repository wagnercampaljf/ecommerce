<?php

namespace common\models;

use yii\db\ActiveQuery;
use Yii;

/**
 * This is the model class for table "ano_modelo".
 *
 * @property string $id
 * @property string $nome
 * @property string $modelo_id
 *
 * @property Veiculo[] $veiculos
 * @property Modelo $modelo
 * @property ProdutoAnoModelo[] $produtoAnoModelos
 * @property Produto[] $produtos
 */
class AnoModelo extends \yii\db\ActiveRecord
{


//    public $nome;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ano_modelo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'modelo_id'], 'required'],
            [['modelo_id'], 'string', 'max' => 10],
//            [['nome'], 'string', 'max' => 255]
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
            'modelo_id' => 'Modelo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculos()
    {
        return $this->hasMany(Veiculo::className(), ['ano_modelo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelo()
    {
        return $this->hasOne(Modelo::className(), ['id' => 'modelo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProdutoAnoModelos()
    {
        return $this->hasMany(ProdutoAnoModelo::className(), ['ano_modelo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::className(), ['id' => 'produto_id'])->viaTable(
            'produto_ano_modelo',
            ['ano_modelo_id' => 'id']
        );
    }

    public static function find()
    {
        return new AnoModeloQuery(get_called_class());
    }

    public function getLabel()
    {
        return ucwords(mb_strtolower($this->nome));
    }

    public function __toString()
    {
        return $this->getLabel();
    }
}

class AnoModeloQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['ano_modelo.nome' => $sort_type]);
    }

    /**
     * Filtra os anos que pertencem ao dado $modelo (id)
     * @author Vinicius Schettino 02/12/2014
     */
    public function byModelo($modelo)
    {
        return $this->innerJoinWith('modelo', false)->andWhere(['modelo.id' => $modelo]);
    }
}