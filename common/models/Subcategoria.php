<?php

namespace common\models;

use linslin\yii2\curl\Curl;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * Este é o model para a tabela "subcategoria".
 *
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 * @property integer $categoria_id
 * @property boolean $ativo
 *
 * @property Produto[] $produtos
 * @property Atributo[] $atributos
 * @property Categoria $categoria
 * @property SubcategoriaDocumentoReferencia[] $subcategoriaDocumentoReferencias
 * @property BannerSubcategoria[] $bannerSubcategorias
 * @property Banner[] $banners
 *
 * @author Igor 09/10/2015
 */
class Subcategoria extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subcategoria';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'categoria_id'], 'required'],
            [['categoria_id'], 'integer'],
            [['nome'], 'string', 'max' => 100],
            [['slug', 'meli_id', 'meli_cat_nome'], 'string', 'max' => 200],
            [['descricao'], 'string'],
            [['ativo'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descricao',
            'categoria_id' => 'Categoria ID',
            'slug' => 'Slug',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtributos()
    {
        return $this->hasMany(Atributo::className(), ['subcategoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::className(), ['subcategoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::className(), ['id' => 'categoria_id'])->inverseOf('subcategorias');;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 07/04/2015
     */
    public function getSubcategoriaDocumentoReferencias()
    {
        return $this->hasMany(SubcategoriaDocumentoReferencia::className(), ['subCategoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 09/10/2015
     */
    public function getBannerSubcategorias()
    {
        return $this->hasMany(BannerSubcategoria::className(), ['subcategoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 09/10/2015
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['id' => 'banner_id'])->viaTable('banner_subcategoria',
            ['subcategoria_id' => 'id']);
    }

    public static function find()
    {
        return new SubcategoriaQuery(get_called_class());
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }

    /**
     * Percore o array de subcategorias e seta o meli_id com sendo o último, que corresponde ao ultimo dropdown
     * @param $postArray
     */
    public function setMeliId($postArray)
    {
        if (isset($postArray['subcat-id'])) {
            foreach ($postArray['subcat-id'] as $subcatId) {
                if (!empty($subcatId)) {
                    $this->meli_id = $subcatId;
                }
            }
            $curl = new Curl();
            $response = $curl->setOption(
                CURLOPT_SSL_VERIFYPEER,
                0)
                ->get('https://api.mercadolibre.com/categories/' . $this->meli_id);
            $this->meli_cat_nome = Json::decode($response)['name'];
        }
    }
}

class SubcategoriaQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['subcategoria.nome' => $sort_type]);
    }

    public function byFabricante($fabricante_id)
    {
        if (is_null($fabricante_id)) {
            return $this;
        }

        return $this->joinWith(['produtos'])->andWhere(['produto.fabricante_id' => $fabricante_id]);
    }
}
