<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "tipo_empresa".
 *
 * @property integer $id
 * @property string $nome
 * @property boolean $juridica
 *
 * @property Empresa[] $empresas
 *
 * @author Ot�vio 06/03/2015
 */
class TipoEmpresa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Ot�vio 06/03/2015
     */
    public static function tableName()
    {
        return 'tipo_empresa';
    }

    /**
     * @inheritdoc
     * @author Ot�vio 06/03/2015
     */
    public function rules()
    {
        return [
            [['id', 'nome'], 'required'],
            [['id'], 'integer'],
            [['juridica'], 'boolean'],
            [['lojista'], 'boolean'],
            [['nome'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     * @author Ot�vio 06/03/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'juridica' => 'Juridica',
            'lojista' => 'Lojista',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 06/03/2015
     */
    public function getEmpresas()
    {
        return $this->hasMany(Empresa::className(), ['id_tipo_empresa' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 06/03/2015
     */
    public static function find()
    {
        return new TipoEmpresaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da TipoEmpresa, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Ot�vio 06/03/2015
 */
class TipoEmpresaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 06/03/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['tipo_empresa.nome' => $sort_type]);
    }

    /**
     * Busca tipos de empresa que relacionados a filiais
     * @return static
     * @author Vitor Horta 16/06/2015
     */
    public function getTipoEmpresaLojista()
    {
        return $this->andWhere(['lojista' => true])->orWhere(['lojista' => null]);
    }

    /**
     * Busca tipos de empresa que relacionados a empresas
     * @return static
     * @author Vitor Horta 16/06/2015
     */
    public function getTipoEmpresaComprador()
    {
        return $this->andWhere(['lojista' => false])->orWhere(['lojista' => null]);
    }
}
