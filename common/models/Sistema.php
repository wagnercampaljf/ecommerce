<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "sistema".
 *
 * @property integer $id
 * @property string $nome
 * @property string $caminho
 *
 * @property Log[] $logs
 *
 * @author Unknown 17/05/2021
 */
class Sistema extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public static function tableName()
    {
        return 'sistema';
    }

    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public function rules()
    {
        return [
            [['nome', 'caminho'], 'required'],
            [['nome'], 'string', 'max' => 200],
            [['caminho'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'caminho' => 'Caminho',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['sistema_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public static function find()
    {
        return new SistemaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Sistema, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 17/05/2021
*/
class SistemaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['sistema.nome' => $sort_type]);
    }
}
