<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "cupom_desconto".
 *
 * @property integer $id
 * @property string $descricao
 * @property string $codigo
 * @property double $valor
 * @property integer $porcentagem
 * @property integer $num_max_utilizacoes
 * @property integer $empresa_id
 * @property integer $filial_id
 *
 * @property Filial $filial
 * @property Empresa $empresa
 *
 * @author Vinicius Schettino 02/12/2014
 */
class CupomDesconto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'cupom_desconto';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'descricao', 'codigo'], 'required'],
            [['id', 'porcentagem', 'num_max_utilizacoes', 'empresa_id', 'filial_id'], 'integer'],
            [['valor'], 'number'],
            [['descricao'], 'string', 'max' => 150],
            [['codigo'], 'string', 'max' => 255]
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
            'descricao' => 'Descricao',
            'codigo' => 'Codigo',
            'valor' => 'Valor',
            'porcentagem' => 'Porcentagem',
            'num_max_utilizacoes' => 'Num Max Utilizacoes',
            'empresa_id' => 'Empresa ID',
            'filial_id' => 'Filial ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['id' => 'empresa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new CupomDescontoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da CupomDesconto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class CupomDescontoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['cupom_desconto.nome' => $sort_type]);
    }
}
