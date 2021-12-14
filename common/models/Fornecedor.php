<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "fornecedor".
 *
 * @property integer $id
 * @property string $nome
 * @property string $razao_social
 * @property string $cpf_cnpj
 * @property string $email
 * @property integer $codigo_fornecedor_omie
 *
 * @author Unknown 29/07/2021
 */
class Fornecedor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/07/2021
     */
    public static function tableName()
    {
        return 'fornecedor';
    }

    /**
     * @inheritdoc
     * @author Unknown 29/07/2021
     */
    public function rules()
    {
        return [
            [['nome', 'razao_social', 'cpf_cnpj', 'email'], 'string'],
            [['codigo_fornecedor_omie'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 29/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'razao_social' => 'Razao Social',
            'cpf_cnpj' => 'Cpf Cnpj',
            'email' => 'Email',
            'codigo_fornecedor_omie' => 'Codigo Fornecedor Omie',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/07/2021
    */
    public static function find()
    {
        return new FornecedorQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Fornecedor, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/07/2021
*/
class FornecedorQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/07/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['fornecedor.nome' => $sort_type]);
    }
}
