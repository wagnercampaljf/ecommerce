<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "modelo".
 *
 * @property string $id
 * @property string $nome
 * @property string $marca_id
 * @property integer $categoria_modelo_id
 *
 * @property Marca $marca
 * @property CategoriaModelo $categoria
 * @property AnoModelo[] $anoModelos
 */
class Modelo extends \yii\db\ActiveRecord implements SearchModel
{
    public $anoModelo;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modelo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'marca_id', 'anoModelo'], 'required'],
            [['marca_id', 'categoria_modelo_id'], 'string', 'max' => 10],
            [['nome'], 'string', 'max' => 255]
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
            'marca_id' => 'Marca',
            'categoria_modelo_id' => 'Categoria de Modelo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarca()
    {
        return $this->hasOne(Marca::className(), ['id' => 'marca_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(CategoriaModelo::className(), ['id' => 'categoria_modelo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnoModelos()
    {
        return $this->hasMany(AnoModelo::className(), ['modelo_id' => 'id']);
    }

    public static function find()
    {
        return new ModeloQuery(get_called_class());
    }

    public function getLabel()
    {
        return ucwords(mb_strtolower($this->nome));
    }

    public function getLabelSearch()
    {
        return $this->getLabel();
    }
}

class ModeloQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['modelo.nome' => $sort_type]);
    }

    /**
     * Filtra os modelos que pertencem a dada $categoria (id)
     * @author Vinicius Schettino 02/12/2014
     */
    public function byCategoria($categoria)
    {
        return $this
            ->andWhere(
                ['categoria_modelo_id' => $categoria]
            );
    }

    /**
     * Filtra os modelos fabricados pela dada $marca (id)
     * @author Vinicius Schettino 02/12/2014
     */
    public function byMarca($marca)
    {
        return $this
            ->andWhere(
                ['marca_id' => $marca]
            );
    }
}
