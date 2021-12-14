<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "mensagem".
 *
 * @property integer $id
 * @property string $assunto
 * @property integer $mensagem
 * @property string $email
 * @property integer $responsabilidade_id
 * @property integer $comprador_id
 *
 * @property Comprador $comprador
 * @property ResponsabilidadeContato $responsabilidade
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Mensagem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'mensagem';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['assunto', 'mensagem', 'responsabilidade_id'], 'required'],
            [['mensagem', 'responsabilidade_id', 'comprador_id'], 'integer'],
            [['assunto', 'email'], 'string', 'max' => 150]
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
            'assunto' => 'Assunto',
            'mensagem' => 'Mensagem',
            'email' => 'Email',
            'responsabilidade_id' => 'Responsabilidade ID',
            'comprador_id' => 'Comprador ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getComprador()
    {
        return $this->hasOne(Comprador::className(), ['id' => 'comprador_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getResponsabilidade()
    {
        return $this->hasOne(ResponsabilidadeContato::className(), ['id' => 'responsabilidade_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new MensagemQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Mensagem, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class MensagemQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['mensagem.nome' => $sort_type]);
    }
}
