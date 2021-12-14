<?php

namespace backend\models;

use Yii;

/**
 * Este é o model para a tabela "historico_compras".
 *
 * @property integer $id
 * @property integer $nota_fiscal_produto_id
 * @property integer $produto_id
 * @property string $valor
 * @property string $dt_compra
 *
 * @author Unknown 06/07/2021
 */
class HistoricoCompras extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 06/07/2021
     */
    public static function tableName()
    {
        return 'historico_compras';
    }

    /**
     * @inheritdoc
     * @author Unknown 06/07/2021
     */
    public function rules()
    {
        return [
            [['nota_fiscal_produto_id', 'produto_id'], 'integer'],
            [['valor'], 'number'],
            [['dt_compra'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 06/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nota_fiscal_produto_id' => 'Nota Fiscal Produto ID',
            'produto_id' => 'Produto ID',
            'valor' => 'Valor',
            'dt_compra' => 'Dt Compra',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/07/2021
    */
    public static function find()
    {
        return new HistoricoComprasQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da HistoricoCompras, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 06/07/2021
*/
class HistoricoComprasQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/07/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['historico_compras.nome' => $sort_type]);
    }
}
