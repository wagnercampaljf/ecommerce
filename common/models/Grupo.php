<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "grupo".
 *
 * @property integer $id
 * @property string $nome
 * @property string $razao
 *
 * @property Empresa[] $empresas
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Grupo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'grupo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 150],
            [['razao'], 'string', 'max' => 200]
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
            'razao' => 'Razao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getEmpresas()
    {
        return $this->hasMany(Empresa::className(), ['grupo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new GrupoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Grupo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class GrupoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['grupo.nome' => $sort_type]);
    }
}
