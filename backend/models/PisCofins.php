<?php

namespace backend\models;

use Yii;

/**
 * Este é o model para a tabela "pis_cofins".
 *
 * @property integer $id
 * @property string $pis_cofins
 * @property string $ncm
 * @property string $data_registro
 *
 * @author Unknown 30/11/2021
 */
class PisCofins extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 30/11/2021
     */
    public static function tableName()
    {
        return 'pis_cofins';
    }

    /**
     * @inheritdoc
     * @author Unknown 30/11/2021
     */
    public function rules()
    {
        return [
            [['pis_cofins', 'ncm'], 'required'],
            ['ncm', 'unique'],
            [['data_registro'], 'safe'],
            [['pis_cofins'], 'string', 'max' => 2],
            [['ncm'], 'string', 'max' => 8]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 30/11/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pis_cofins' => 'CST',
            'ncm' => 'NCM',
            'data_registro' => 'Data Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 30/11/2021
     */
    public static function find()
    {
        return new PisCofinsQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PisCofins, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 30/11/2021
 */
class PisCofinsQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 30/11/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pis_cofins.nome' => $sort_type]);
    }
}
