<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "marca".
 *
 * @property string $id
 * @property string $nome
 * @property resource $imagem
 * @property string $slug
 *
 * @property Modelo[] $modelos
 *
 * @author Unknown 25/09/2018
 */
class MarcaModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 25/09/2018
     */
    public static function tableName()
    {
        return 'marca';
    }

    /**
     * @inheritdoc
     * @author Unknown 25/09/2018
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['imagem'], 'string'],
            [['nome'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 25/09/2018
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'imagem' => 'Imagem',
            'slug' => 'Slug',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2018
    */
    public function getModelos()
    {
        return $this->hasMany(Modelo::className(), ['marca_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2018
    */
    public static function find()
    {
        return new MarcaModelQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MarcaModel, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 25/09/2018
*/
class MarcaModelQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 25/09/2018
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['marca.nome' => $sort_type]);
    }
}
