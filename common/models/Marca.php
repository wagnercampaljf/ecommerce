<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "marca".
 *
 * @property string $id
 * @property string $nome
 * @property resource $imagem
 *
 * @property Modelo[] $modelos
 */
class Marca extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marca';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'slug'], 'required'],
            [['id'], 'string', 'max' => 10],
            [
                'imagem',
                'image',
                'extensions' => 'png, jpg',
                'maxSize' => 500000,
            ],
            [['nome'], 'string', 'max' => 255]
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
            'slug' => 'Slug',
            'imagem' => 'Imagem'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelos()
    {
        return $this->hasMany(Modelo::className(), ['marca_id' => 'id']);
    }

    public static function find()
    {
        return new MarcaQuery(get_called_class());
    }

    public function getLabel()
    {
        return ucwords(mb_strtolower($this->nome));
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }

    /**
     * Retorna o html da imagem do produto
     * @author Igor Mageste
     * @param array $options Opções para a tag img
     * @return string
     */
    public function getImage($options = [])
    {
        $src = $this->getUrlImage();

        return Html::img($src, $options);
    }

    /**
     * @author Igor Mageste
     * @since 29/06/2016
     * @return string
     */
    public function getUrlImage()
    {
        $src = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        if (!is_null($this->imagem)) {
            $src = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?codigo=' . $this->codigo_global;
        }

        return $src;
    }

    public function getImg($options)
    {
        if (is_string($this->imagem)) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . $this->imagem, $options);
        }
        if ($this->imagem) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . stream_get_contents($this->imagem), $options);
        }
        $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
        return Html::img($src, $options);
    }
}

class MarcaQuery extends ActiveQuery
{
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['marca.nome' => $sort_type]);
    }

    /**
     * Filtra as marcas que possuem veículos de dada $categoria (id)
     * não é possível evitar o join, já que a relação entre categoria e marca tem
     * que passar pelo modelo (já que uma marca pode fazer veiculos de várias categorias).
     * @see o MER
     * @author Vinicius Schettino 02/12/2014
     */
    public function byCategoria($categoria)
    {
        return $this
            ->innerJoinWith(
                ['modelos', 'modelos.categoria'],
                false
            )
            ->andWhere(
                ['categoria_modelo.id' => $categoria]
            );
    }

    /**
     * Filtra as marcas que possuem veículos de dada $categoria (id)
     * não é possível evitar o join, já que a relação entre categoria e marca tem
     * que passar pelo modelo (já que uma marca pode fazer veiculos de várias categorias).
     * @see o MER
     * @author Otavio Augusto 24/10/2016
     */
    public function byCategoriaSlug($categoriaSlug)
    {
        return $this
            ->innerJoinWith(
                ['modelos', 'modelos.categoria'],
                false
            )
            ->andWhere(
                ['categoria_modelo.slug' => $categoriaSlug]
            );
    }
}

