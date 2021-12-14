<?php

namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * Este é o model para a tabela "processamento".
 *
 * @property integer $id
 * @property integer $funcao_id
 * @property string $data_hora_inicial
 * @property string $data_hora_final
 * @property string $observacao
 * @property string $status
 * @property string $path
 * @property string coluna_codigo_fabricante$file_planilha
 * @property integer $coluna_codigo_fabricante
 * @property string $coluna_estoque
 * @property integer $coluna_preco
 * @property integer $coluna_nome
 * 
 * 
 * @property integer $coluna_preco_compra
 * @property integer $coluna_capas
 * @property string $parametros
 *
 * @property Funcao $funcao
 *
 * @author Unknown 08/07/2021
 */
class Processamento extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public static function tableName()
    {
        return 'processamento';
    }
    //public $file_planilha;
   // public $coluna_codigo_fabricante;
//public $coluna_estoque;
    //public $coluna_preco;
    public $coluna_capas;


    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function rules()
    {
        return [

            [['funcao_id'], 'required'],
            [['coluna_codigo_fabricante','coluna_estoque','coluna_preco', 'coluna_preco_compra','coluna_capas','coluna_nome'], 'integer'],
            [['funcao_id'], 'default', 'value' => null],
            [['funcao_id'], 'integer'],
            [['data_hora_inicial', 'data_hora_final'], 'safe'],
            [['observacao', 'status', 'parametros'], 'string'],
            [['parametros',], 'string'],
             [['parametros',], 'string'],
            [['funcao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Funcao::className(), 'targetAttribute' => ['funcao_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'funcao_id' => 'Função',
            'data_hora_inicial' => 'Data Hora Inicial',
            'data_hora_final' => 'Data Hora Final',
            'observacao' => 'Observacao',
            'status' => 'Status',
            'paramentros' => 'Paramentros',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */

    public function getFuncao()
    {
        return $this->hasOne(Funcao::className(), ['id' => 'funcao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */

    public static function find()
    {
        return new ProcessamentoQuery(get_called_class());
    }

}

/**
 * Classe para contenção de escopos da Processamento, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 08/07/2021
*/

class ProcessamentoQuery extends \yii\db\ActiveQuery

{

    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */

    public function ordemAlfabetica($sort_type = SORT_ASC)

    {
        return $this->orderBy(['processamento.nome' => $sort_type]);
    }

}
