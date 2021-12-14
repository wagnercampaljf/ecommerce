<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "subcategoria_documento_referencia".
 *
 * @property integer $id
 * @property integer $documentoReferencia_id
 * @property integer $subCategoria_id
 *
 * @property DocumentoReferencia $documentoReferencia
 * @property Subcategoria $subCategoria
 *
 * @author Igor 07/04/2015
 */
class SubcategoriaDocumentoReferencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 07/04/2015
     */
    public static function tableName()
    {
        return 'subcategoria_documento_referencia';
    }

    /**
     * @inheritdoc
     * @author Igor 07/04/2015
     */
    public function rules()
    {
        return [
            [['documentoReferencia_id', 'subCategoria_id'], 'required'],
            [['documentoReferencia_id', 'subCategoria_id'], 'integer']
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
            'documentoReferencia_id' => 'Documento Referencia ID',
            'subCategoria_id' => 'Sub Categoria ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function getDocumentoReferencia()
    {
        return $this->hasOne(DocumentoReferencia::className(), ['id' => 'documentoReferencia_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function getSubCategoria()
    {
        return $this->hasOne(Subcategoria::className(), ['id' => 'subCategoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public static function find()
    {
        return new SubcategoriaDocumentoReferenciaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da SubcategoriaDocumentoReferencia, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 07/04/2015
 */
class SubcategoriaDocumentoReferenciaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['subcategoria_documento_referencia.nome' => $sort_type]);
    }
}
