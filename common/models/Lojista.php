<?php

namespace common\models;

use yii\db\ActiveQuery;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "lojista".
 *
 * @property integer $id
 * @property string $razao
 * @property string $dt_criacao
 * @property resource $imagem
 * @property string $documento
 * @property boolean $juridica
 * @property boolean $aprovado
 * @property string $motivo_veredito
 *
 * @property Filial[] $filials
 */
class Lojista extends \yii\db\ActiveRecord
{
    CONST LIMITE_P_INICIAL = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lojista';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['razao', 'documento'], 'required'],
            ['imagem', 'required', 'on' => 'upload'],
            [['imagem', 'dt_criacao'], 'safe'],
            [['contrato_correios', 'senha_correios'], 'string'],
            [['juridica', 'aprovado'], 'boolean'],
            [['razao'], 'string', 'max' => 150],
            [['documento'], 'string', 'max' => 20],
            [['motivo_veredito'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'razao' => 'Razão',
            'dt_criacao' => 'Data Criação',
            'imagem' => 'Imagem',
            'documento' => 'Documento',
            'juridica' => 'Jurídica',
            'aprovado' => 'Aprovado',
            'motivo_veredito' => 'Motivo Veredito',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilials()
    {
        return $this->hasMany(Filial::className(), ['lojista_id' => 'id'])->inverseOf('lojista');
    }

    public static function find()
    {
        return new LojistaQuery(get_called_class());
    }

    public function getImage($options = [])
    {
        return Html::img($this->getUrlImage(), $options);
    }

    public function getUrlImage()
    {
        $src = Url::base() . '/frontend/web/assets/img/produtos/no-image.png';
        if (!is_null($this->imagem)) {
            $src = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-lojista?id=' . $this->id;
        }

        return $src;
    }
}

class LojistaQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['lojista.nome' => $sort_type]);
    }

    /**
     * Se o lojista está marcado para aparecer na página inicial
     * @param $limit int quantidade máxima de resultados a serem retornados
     * @author Vinicius Schettino 02/12/2014
     */
    public function paginaInicial($pinicial = true, $limit = '')
    {
        if ($limit == '') {
            $limit = Lojista::LIMITE_P_INICIAL;
        }

        return $this->andWhere(['lojista.pagina_inicial' => 'true'])->limit($limit);
    }

    /**
     * Se o lojista está ativo
     * @author Vinicius Schettino 02/12/2014
     */
    public function ativo($ativo = true)
    {
        return $this->orderBy(['lojista.ativo' => $ativo]);
    }

    /**
     * Se o lojista está aprovado pelos administradores atuar no sistema
     * @author Vinicius Schettino 02/12/2014
     */
    public function aprovado($aprovado = true)
    {
        return $this->orderBy(['lojista.aprovado' => $aprovado]);
    }
}