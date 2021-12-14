<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "cidade".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $estado_id
 *
 * @property EnderecoFilial[] $enderecoFilials
 * @property EnderecoEmpresa[] $enderecoEmpresas
 * @property Estado $estado
 * @property Banner[] $banners
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Cidade extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'cidade';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'estado_id'], 'required'],
            [['id', 'estado_id'], 'integer'],
            [['nome'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'estado_id' => Yii::t('app', 'Estado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEstado()
    {
        return $this->hasOne(Estado::className(), ['id' => 'estado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEnderecoEmpresas()
    {
        return $this->hasMany(EnderecoEmpresa::className(), ['cidade_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEnderecoFilials()
    {
        return $this->hasMany(EnderecoFilial::className(), ['cidade_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/10/2015
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['cidade_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new CidadeQuery(get_called_class());
    }

    public function getLabel()
    {
        return $this->nome . ' (' . $this->estado->sigla . ')';
    }

    public function __toString()
    {
        return $this->getLabel();
    }

    public function getLabelSearch()
    {
        return $this->getLabel();
    }
}

/**
 * Classe para contenção de escopos da Cidade, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class CidadeQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['cidade.nome' => $sort_type]);
    }

    /**
     * Filtra as cidades de acordo com a SubCategoria escolhida nos filtros
     * @return \yii\db\ActiveQuery
     * @author Igor Mageste 24/02/2015
     */
    public function bySubCategoria($subcategoria_id)
    {
        return $this->innerJoin('endereco_filial "EF"', '"EF".cidade_id = "cidade"."id"')->innerJoin('filial "F"',
            '"F"."id" = "EF".filial_id')->innerJoin('produto_filial "PF"',
            '"PF".filial_id = "F"."id"')->innerJoin('produto "P"',
            '"P"."id" = "PF".produto_id')->andWhere(['"P".subcategoria_id' => $subcategoria_id])->andWhere('"PF".quantidade > 0');
    }

    /**
     * @param $nome
     * @return static
     * @author Igor Mageste 07/10/2015
     */
    public function byNome($nome)
    {
        if (is_null($nome)) {
            return $this;
        }

        return $this->andFilterWhere(['like', 'lower(cidade.nome)', strtolower($nome)]);
    }

    public function byEstado($estado_id)
    {
        if (is_null($estado_id)) {
            return $this;
        }

        return $this->joinWith(['estado'])->andFilterWhere(['estado.id' => $estado_id]);
    }
}
