<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "mercado_livre_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $resource
 * @property string $topic
 * @property string $received
 * @property integer $application_id
 * @property string $sent
 * @property integer $attempts
 *
 * @author Unknown 17/09/2018
 */
class MercadoLivreOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 17/09/2018
     */
    public static function tableName()
    {
        return 'mercado_livre_order';
    }

    /**
     * @inheritdoc
     * @author Unknown 17/09/2018
     */
    public function rules()
    {
        return [
            [['user_id', 'application_id', 'attempts'], 'integer'],
            [['resource', 'topic', 'received', 'sent'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 17/09/2018
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'resource' => 'Resource',
            'topic' => 'Topic',
            'received' => 'Received',
            'application_id' => 'Application ID',
            'sent' => 'Sent',
            'attempts' => 'Attempts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/09/2018
    */
    public static function find()
    {
        return new MercadoLivreOrderQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MercadoLivreOrder, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 17/09/2018
*/
class MercadoLivreOrderQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/09/2018
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['mercado_livre_order.nome' => $sort_type]);
    }
}
