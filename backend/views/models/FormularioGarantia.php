<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "formulario_garantia".
 *
 * @property integer $id
 * @property string $nome
 * @property string $email
 * @property string $data_compra
 * @property string $razao_social
 * @property string $nr_nf_compra
 * @property string $codigo_peca_seis_digitos
 * @property string $modelo_do_veiculo
 * @property string $ano
 * @property string $chassi
 * @property string $numero_de_serie_do_motor
 * @property string $data_aplicacao
 * @property string $km_montagem
 * @property string $km_defeito
 * @property string $contato
 * @property string $telefone
 * @property string $descricao_do_defeito_apresentado
 *
 * @author Unknown 06/08/2020
 */
class FormularioGarantia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 06/08/2020
     */
    public static function tableName()
    {
        return 'formulario_garantia';
    }

    /**
     * @inheritdoc
     * @author Unknown 06/08/2020
     */
    public function rules()
    {
        return [
            [['data_compra', 'data_aplicacao'], 'safe'],
            [['razao_social', 'nr_nf_compra', 'codigo_peca_seis_digitos', 'modelo_do_veiculo', 'ano', 'chassi', 'numero_de_serie_do_motor', 'km_montagem', 'km_defeito', 'contato', 'telefone', 'descricao_do_defeito_apresentado'], 'string'],
            [['nome', 'email'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 06/08/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => '*Nome:',
            'email' => '*Emai:',
            'data_compra' => '*Data Compra:',
            'razao_social' => '*Razão Social:',
            'nr_nf_compra' => '*NR.NF.Compra:',
            'codigo_peca_seis_digitos' => '*Código peça 6 dígitos:',
            'modelo_do_veiculo' => '*Modelo do Veículo:',
            'ano' => '*Ano:',
            'chassi' => '*Chassi:',
            'numero_de_serie_do_motor' => '*Número de série do motor:',
            'data_aplicacao' => '*Data Aplicação:',
            'km_montagem' => '*KM Montagem:',
            'km_defeito' => '*KM Defeito:',
            'contato' => '*Contato:',
            'telefone' => '*Telefone:',
            'descricao_do_defeito_apresentado' => '*Descrição do defeito apresentado:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/08/2020
    */
    public static function find()
    {
        return new FormularioGarantiaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da FormularioGarantia, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 06/08/2020
*/
class FormularioGarantiaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/08/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['formulario_garantia.nome' => $sort_type]);
    }
}
