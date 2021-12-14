<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "fabricante".
 *
 * @property integer $id
 * @property string $nome
 * @property string $sac
 *
 * @property Produto[] $produtos
 * @property Banner[] $banners
 */
class Fabricante extends \yii\db\ActiveRecord implements SearchModel
{
    CONST LIMITE_P_INICIAL = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fabricante';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 150],
            [['sac'], 'string', 'max' => 30]
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
            'sac' => 'Sac',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::className(), ['fabricante_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor Mageste 09/10/2015
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['fabricante_id' => 'id']);
    }

    public static function find()
    {
        return new FabricanteQuery(get_called_class());
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }
}

class FabricanteQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['fabricante.nome' => $sort_type]);
    }

    /**
     * Se o fabricante está marcado para aparecer na página inicial
     * @param $limit int quantidade máxima de resultados a serem retornados
     * @author Vinicius Schettino 02/12/2014
     */
    public function paginaInicial($pinicial = true, $limit = '')
    {
        if ($limit == '') {
            $limit = Fabricante::LIMITE_P_INICIAL;
        }

        return $this->andWhere(['fabricante.pagina_inicial' => 'true'])->limit($limit);
    }

    public function bySubcategoria($subcategoria_id)
    {
        if (is_null($subcategoria_id)) {
            return $this;
        }

        return $this->joinWith(['produtos'])->andWhere(['produto.subcategoria_id' => $subcategoria_id]);
    }

    public function byCategoria($categoria_id)
    {
        if (is_null($categoria_id)) {
            return $this;
        }

        return $this->joinWith([
            'produtos',
            'produtos.subcategoria'
        ])->andWhere(['subcategoria.categoria_id' => $categoria_id]);
    }
}
