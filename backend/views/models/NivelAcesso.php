<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "nivel_acesso".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $nivel
 * @property integer $tipo
 *
 * @property Usuario[] $usuarios
 * @property Comprador[] $compradors
 *
 * @author Vinicius Schettino 02/12/2014
 */
class NivelAcesso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'nivel_acesso';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'nivel', 'tipo'], 'required'],
            [['id', 'nivel', 'tipo'], 'integer'],
            [['nome'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'nivel' => 'Nivel',
            'tipo' => 'Tipo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['nivel_acesso_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getCompradors()
    {
        return $this->hasMany(Comprador::className(), ['nivel_acesso_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new NivelAcessoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da NivelAcesso, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class NivelAcessoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nivel_acesso.nome' => $sort_type]);
    }
}
