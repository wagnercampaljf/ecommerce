<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "responsabilidade_contato".
 *
 * @property integer $id
 * @property string $nome
 * @property string $email
 *
 * @property Mensagem[] $mensagems
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ResponsabilidadeContato extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'responsabilidade_contato';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'email'], 'required'],
            [['nome'], 'string', 'max' => 60],
            [['email'], 'string', 'max' => 150]
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
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getMensagems()
    {
        return $this->hasMany(Mensagem::className(), ['responsabilidade_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new ResponsabilidadeContatoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ResponsabilidadeContato, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class ResponsabilidadeContatoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['responsabilidade_contato.nome' => $sort_type]);
    }
}
