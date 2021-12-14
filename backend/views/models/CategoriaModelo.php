<?php

namespace common\models;

use yii\db\ActiveQuery;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "categoria_modelo".
 *
 * @property integer $id
 * @property string $nome
 * @property resource $foto
 *
 * @property Modelo[] $modelos
 */
class CategoriaModelo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categoria_modelo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome', 'foto'], 'string']
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
            'foto' => 'Foto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelos()
    {
        return $this->hasMany(Modelo::className(), ['categoria_id' => 'id']);
    }

    public static function find()
    {
        return new CategoriaModeloQuery(get_called_class());
    }

    public function getImage($options = [])
    {
        echo base64_decode(stream_get_contents($this->foto));
    }

}

class CategoriaModeloQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['categoria_modelo.nome' => $sort_type]);
    }
}