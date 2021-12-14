<?php

namespace backend\models;

use common\models\Filial;
use Yii;

/**
 * Este é o model para a tabela "conta_corrente".
 *
 * @property integer $id
 * @property string $descricao
 * @property integer $filial_id
 * @property integer $codigo_conta_corrente_omie
 *
 * @property Filial $filial
 *
 * @author Unknown 03/09/2021
 */
class ContaCorrente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 03/09/2021
     */
    public static function tableName()
    {
        return 'conta_corrente';
    }

    /**
     * @inheritdoc
     * @author Unknown 03/09/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'filial_id', 'codigo_conta_corrente_omie'], 'required'],
            [['filial_id', 'codigo_conta_corrente_omie'], 'default', 'value' => null],
            [['filial_id', 'codigo_conta_corrente_omie'], 'integer'],
            [['descricao'], 'string', 'max' => 300],
            [['filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filial::class, 'targetAttribute' => ['filial_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 03/09/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
            'filial_id' => 'Filial ID',
            'codigo_conta_corrente_omie' => 'Codigo Conta Corrente Omie',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 03/09/2021
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::class, ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 03/09/2021
     */
    public static function find()
    {
        return new ContaCorrenteQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ContaCorrente, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 03/09/2021
 */
class ContaCorrenteQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 03/09/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['conta_corrente.nome' => $sort_type]);
    }
}
