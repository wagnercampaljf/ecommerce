<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "log".
 *
 * @property integer $id
 * @property string $descricao
 * @property string $salvo_em
 * @property integer $salvo_por
 * @property integer $sistema_id
 * @property string $tabela_origem
 * @property integer $id_origem
 * @property integer $log_operacao_id
 *
 * @property LogOperacao $logOperacao
 * @property Administrador $salvoPor
 * @property Sistema $sistema
 *
 * @author Unknown 17/05/2021
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public function rules()
    {
        return [
            [['descricao'], 'string'],
            [['salvo_em', 'salvo_por', 'tabela_origem', 'id_origem'], 'required'],
            [['salvo_em'], 'safe'],
            [['salvo_por', 'sistema_id', 'id_origem', 'log_operacao_id'], 'default', 'value' => null],
            [['salvo_por', 'sistema_id', 'id_origem', 'log_operacao_id'], 'integer'],
            [['tabela_origem'], 'string', 'max' => 200],
            [['salvo_por'], 'exist', 'skipOnError' => true, 'targetClass' => Administrador::className(), 'targetAttribute' => ['salvo_por' => 'id']],
            [['log_operacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => LogOperacao::className(), 'targetAttribute' => ['log_operacao_id' => 'id']],
            [['sistema_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sistema::className(), 'targetAttribute' => ['sistema_id' => 'id']]
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
            'descricao' => 'Descricao',
            'salvo_em' => 'Salvo Em',
            'salvo_por' => 'Salvo Por',
            'sistema_id' => 'Sistema ID',
            'tabela_origem' => 'Tabela Origem',
            'id_origem' => 'Id Origem',
            'log_operacao_id' => 'Log Operacao ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function getLogOperacao()
    {
        return $this->hasOne(LogOperacao::className(), ['id' => 'log_operacao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function getSalvoPor()
    {
        return $this->hasOne(Administrador::className(), ['id' => 'salvo_por']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function getSistema()
    {
        return $this->hasOne(Sistema::className(), ['id' => 'sistema_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public static function find()
    {
        return new LogQuery(get_called_class());
    }
    
    public static function registrarLog($descricao, $tabela_origem, $id_origem, $sistema_id, $log_operacao_id){
               
        //Sistema:
        //1-Backend
        //2-Frontend
        //3-Lojista
        //4-Console
        
        //Tipo Operação:
        //1-Insert
        //2-Update
        //3-Delete
        
        //echo "(((";var_dump(isset(Yii::$app->user)); echo ")))"; die;

	if(isset(Yii::$app->user)){
		if(Yii::$app->user->id != 1){
        		$log                    = new Log;
        		$log->salvo_em          = date("Y-m-d H:i:s");
        		$log->salvo_por         = isset(Yii::$app->user) ? Yii::$app->user->id : 1;
        		$log->descricao         = $descricao;
        		$log->tabela_origem     = $tabela_origem;
        		$log->id_origem         = $id_origem;
        		$log->sistema_id        = isset(Yii::$app->user) ? $sistema_id : 4;;
        		$log->log_operacao_id   = $log_operacao_id;
        		$log->save();
		}
	}

    }
}

/**
 * Classe para contenção de escopos da Log, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 17/05/2021
*/
class LogQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['log.nome' => $sort_type]);
    }
}
