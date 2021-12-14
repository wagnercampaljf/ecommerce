<?php

namespace common\models;

use linslin\yii2\curl\Curl;
use Yii;

/**
 * This is the model class for table "categoria".
 *
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 *
 * @property Subcategoria[] $subcategorias
 */
class Categoria extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categoria';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 100],
            [['slug', 'meli_id', 'meli_cat_nome'], 'string', 'max' => 200],
            [['descricao'], 'string']
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
            'slug' => 'Slug',
        ];
    }

    public static function getCategoriasMeli()
    {
        $curl = new Curl();
        $response = $curl->setOption(
            CURLOPT_SSL_VERIFYPEER,
            0)
            ->get('https://api.mercadolibre.com/sites/MLB/categories');

        return $response;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubcategoriasNaoVazias($ordemAlfabetica = true)
    {
        $subCategorias = Subcategoria::find()->andWhere([
            'categoria_id' => $this->id,
            'ativo' => 't'
        ])->all();
        $subCategoriasNaoVazias = array();
        foreach ($subCategorias as $subCategoria) {
            if (!empty($subCategoria->produtos)) {
                $subCategoriasNaoVazias[] = $subCategoria;
            }
        }

        return $subCategoriasNaoVazias;
    }

    public function getSubcategoriasAtivas($ordemAlfabetica = true)
    {
        $subCategorias = Subcategoria::find()->andWhere([
            'categoria_id' => $this->id,
            'ativo' => 't'
        ])->orderBy('nome')->all();
        return $subCategorias;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubcategorias($ordemAlfabetica = true)
    {
        $query = $this->hasMany(Subcategoria::className(), ['categoria_id' => 'id'])->inverseOf('categoria');;
        if ($ordemAlfabetica) {
            return $query->ordemAlfabetica();
        }
        return $query;
    }

    public static function find()
    {
        return new CategoriaQuery(get_called_class());
    }

    /**
     * Monta o array de categorias já com o as subcategorias para
     * utilização no menu principal de categorias
     * @author Vinicius Schettino 28/11/2014
     * @return array categorias e subcategorias
     */
    public static function getArrayMenu()
    {
        $categorias = (Categoria::find()->innerJoinWith('subcategorias')->ordemAlfabetica()->all());
        $categoriasItems = [];
        foreach ($categorias as $categoria) {
            $subcategorias = $categoria->subcategoriasAtivas;
            if (!empty($subcategorias)) {
                $categoriaItem = [
                    'label' => $categoria->nome,
                    'url' => [
                        '/auto/' . $categoria->slug,
                    ]
                ];
                $subcategoriasItems = [];
                foreach ($subcategorias as $subcategoria) {
                    if ($subcategoria->ativo) {
                        $subcategoriasItems[] = [
                            'label' => $subcategoria->nome,
                            'url' => [
                                '/auto/' . $subcategoria->categoria->slug . '/' . $subcategoria->slug,
//                            'categoria_id' => $subcategoria->categoria_id,
//                            'subcategoria_id' => $subcategoria->id
                            ]
                        ];
                    }
                }
                $categoriaItem['items'] = $subcategoriasItems;
                $categoriasItems[] = $categoriaItem;
            }
        }
        return $categoriasItems;
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }
}


class CategoriaQuery extends \yii\db\ActiveQuery
{

    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['categoria.nome' => $sort_type]);
    }
}
