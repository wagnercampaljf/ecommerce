<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * Este é o model para a tabela "documento_referencia".
 *
 * @property integer $id
 * @property string $nome
 * @property string $extensao
 * @property string $label
 *
 * @property SubcategoriaDocumentoReferencia[] $subcategoriaDocumentoReferencias
 *
 * @author Igor 07/04/2015
 */
class DocumentoReferencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 07/04/2015
     */
    public static function tableName()
    {
        return 'documento_referencia';
    }

    /**
     * @inheritdoc
     * @author Igor 07/04/2015
     */
    public function rules()
    {
        return [
            [['nome', 'extensao', 'label'], 'required'],
            [['nome', 'label'], 'string', 'max' => 250],
            [['extensao'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 07/04/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'extensao' => 'Extensão',
            'label' => 'Label',
        ];
    }

    public function getNome($extensao = true)
    {
        if ($extensao) {
            return $this->nome . '.' . $this->extensao;
        } else {
            return $this->nome;
        }
    }

    public function getHref()
    {
        return Url::to('@pdfs/') . $this->getNome();
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function getSubcategoriaDocumentoReferencias()
    {
        return $this->hasMany(SubcategoriaDocumentoReferencia::className(), ['id_documentoReferencia' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public static function find()
    {
        return new DocumentoReferenciaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da DocumentoReferencia, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 07/04/2015
 */
class DocumentoReferenciaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['documento_referencia.nome' => $sort_type]);
    }
}
