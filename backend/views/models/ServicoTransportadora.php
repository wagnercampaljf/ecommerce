<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "servico_transportadora".
 *
 * @property integer $id
 * @property string $nome
 * @property string $codigo
 * @property integer $transportadora_id
 *
 * @property Pedido[] $pedidos
 * @property FilialServicoTransportadora[] $filialServicoTransportadoras
 * @property Filial[] $filials
 * @property Transportadora $transportadora
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ServicoTransportadora extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'servico_transportadora';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'transportadora_id'], 'required'],
            [['id', 'transportadora_id'], 'integer'],
            [['nome'], 'string', 'max' => 255],
            [['codigo'], 'string', 'max' => 10]
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
            'codigo' => 'Codigo',
            'transportadora_id' => 'Transportadora ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::className(), ['servico_transportadora_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getFilialServicoTransportadoras()
    {
        return $this->hasMany(FilialServicoTransportadora::className(), ['servico_transportadora_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getFilials()
    {
        return $this->hasMany(Filial::className(), ['id' => 'filial_id'])->viaTable('filial_servico_transportadora', ['servico_transportadora_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getTransportadora()
    {
        return $this->hasOne(Transportadora::className(), ['id' => 'transportadora_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new ServicoTransportadoraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ServicoTransportadora, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class ServicoTransportadoraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['servico_transportadora.nome' => $sort_type]);
    }
}
