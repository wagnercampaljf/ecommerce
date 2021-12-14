<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Este é o model para a tabela "banner".
 *
 * @property integer $id
 * @property resource $pdf
 * @property string $link
 * @property resource $imagem
 * @property string $data_inicio
 * @property string $data_fim
 * @property string $nome
 * @property integer $qt_cliques
 * @property integer $fabricante_id
 * @property integer $produto_id
 * @property integer $posicao_id
 * @property integer $cidade_id
 * @property string $descricao
 *
 * @property BannerSubcategoria[] $bannerSubcategorias
 * @property Subcategoria[] $subcategorias
 * @property Fabricante $fabricante
 * @property Cidade $cidade
 * @property Produto $produto
 * @property CategoriaBanner[] $categoriaBanners
 * @property PosicaoBanner $posicao
 *
 * @author Igor 06/10/2015
 */
class Banner extends \yii\db\ActiveRecord
{
    public $subcategoria_id;
    public $categoriaBanner_id;

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public static function tableName()
    {
        return 'banner';
    }

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public function rules()
    {
        return [
            [['data_inicio', 'data_fim', 'nome', 'posicao_id', 'categoriaBanner_id'], 'required'],
            [['imagem'], 'required', 'on' => ['create']],
            [['data_inicio', 'data_fim'], 'date', 'format' => 'php:d/m/Y'],
            [
                ['data_fim'],
                'compare',
                'compareAttribute' => 'data_inicio',
                'operator' => '>=',
                'message' => 'Data de Início tem que ser maior'
            ],
            [
                'imagem',
                'image',
                'extensions' => 'png, jpg',
                'maxSize' => 500000,
            ],
            [['pdf'], 'file', 'extensions' => 'pdf'],
            [
                ['pdf'],
                'required',
                'when' => function ($model) {
                    return is_null($model->link);
                },
                'whenClient' => 'function(){return !$("#tipo-link").bootstrapSwitch(\'state\');}',
                'on' => 'create'
            ],
            [
                [
                    'qt_cliques',
                    'fabricante_id',
                    'produto_id',
                    'posicao_id',
                    'cidade_id',
                ],
                'integer'
            ],
            [
                ['link'],
                'required',
                'when' => function ($model) {
                    return is_null($model->pdf);
                },
                'whenClient' => 'function(){return $("#tipo-link").bootstrapSwitch(\'state\');}'
            ],
            [['link', 'descricao'], 'string', 'max' => 400],
            [['nome'], 'string', 'max' => 300],
            [['subcategoria_id', 'categoriaBanner_id'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pdf' => Yii::t('app', 'PDF'),
            'link' => Yii::t('app', 'URL'),
            'imagem' => Yii::t('app', 'Imagem'),
            'data_inicio' => Yii::t('app', 'Data Inicio'),
            'data_fim' => Yii::t('app', 'Data Fim'),
            'nome' => Yii::t('app', 'Nome'),
            'qt_cliques' => Yii::t('app', 'Nr Cliques'),
            'fabricante_id' => Yii::t('app', 'Fabricante'),
            'produto_id' => Yii::t('app', 'Produto'),
            'posicao_id' => Yii::t('app', 'Posição'),
            'cidade_id' => Yii::t('app', 'Cidade'),
            'subcategoria_id' => Yii::t('app', 'SubCategoria'),
            'descricao' => Yii::t('app', 'Descrição'),
            'categoriaBanner_id' => Yii::t('app', 'Categoria Banner'),
        ];
    }

    public function getBannerCategoriaBanners()
    {
        return $this->hasMany(BannerCategoriaBanner::className(), ['banner_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriaBanners()
    {
        return $this->hasMany(CategoriaBanner::className(), ['id' => 'categoria_banner_id'])->viaTable('banner_categoria_banner', ['banner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getBannerSubcategorias()
    {
        return $this->hasMany(BannerSubcategoria::className(), ['banner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getSubcategorias()
    {
        return $this->hasMany(Subcategoria::className(), ['id' => 'subcategoria_id'])->viaTable('banner_subcategoria', ['banner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getFabricante()
    {
        return $this->hasOne(Fabricante::className(), ['id' => 'fabricante_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getCidade()
    {
        return $this->hasOne(Cidade::className(), ['id' => 'cidade_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

//    /**
//     * @return \yii\db\ActiveQuery
//     * @author Igor 06/10/2015
//     */
//    public function getCategoriaBanner()
//    {
//        return $this->hasOne(CategoriaBanner::className(), ['id' => 'categoria_banner_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getPosicao()
    {
        return $this->hasOne(PosicaoBanner::className(), ['id' => 'posicao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public static function find()
    {
        return new BannerQuery(get_called_class());
    }

    /**
     * @author Igor Mageste 06/10/2015
     * @return string
     */
    public function getLabelLink()
    {
        if ($this->link) {
            return Html::a($this->link, Url::to($this->link), ['target' => '_blank']);
        } else {
            return Html::a('pdf', Url::to('data:application/pdf;base64,' . stream_get_contents($this->pdf)),
                ['target' => '_blank']);
        }
    }

    public function beforeSave($insert)
    {
        $this->data_inicio = date_create_from_format('d/m/Y', $this->data_inicio);
        $this->data_inicio = $this->data_inicio->format('Y-m-d');
        $this->data_fim = date_create_from_format('d/m/Y', $this->data_fim);
        $this->data_fim = $this->data_fim->format('Y-m-d');

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->data_inicio = Yii::$app->formatter->asDate($this->data_inicio, 'php:d/m/Y');
        $this->data_fim = Yii::$app->formatter->asDate($this->data_fim, 'php:d/m/Y');
    }

    public function getLabelSubcategorias()
    {
        if ($this->subcategorias) {
            return Html::tag('ul',
                Html::tag('li', implode('</li><li>', ArrayHelper::getColumn($this->subcategorias, 'nome'))));
        }

        return null;
    }

    public function getLabelCategoriaBanners()
    {
        if ($this->categoriaBanners) {
            return Html::tag('ul',
                Html::tag('li', implode('</li><li>', ArrayHelper::getColumn($this->categoriaBanners, 'nome'))));
        }

        return null;
    }

    public function getImg($options)
    {
        if ($this->imagem) {
            $options = ArrayHelper::merge(
                ['width' => $this->posicao->largura, 'heigth' => $this->posicao->altura],
                $options
            );

            return Html::img('data:image;base64,' . stream_get_contents($this->imagem), $options);
        }

        $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
        return Html::img($src, $options);
    }

    public function Slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}

/**
 * Classe para contenção de escopos da Banner, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 06/10/2015
 */
class BannerQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['banner.nome' => $sort_type]);
    }

    public function ativo()
    {
        return $this->andWhere(new Expression("'" . date('Y-m-d') . '\' BETWEEN "banner"."data_inicio" AND "banner"."data_fim"'))->orderBy(['data_inicio' => SORT_DESC]);
    }

    /**
     * @author Igor Mageste 09/10/2015
     * @param $cidade_id
     * @return static
     */
    public function byCidade($cidade_id)
    {
        return $this->innerJoinWith(['cidade'])->andWhere(['banner.cidade_id' => $cidade_id]);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $posicao_id
     * @return $this
     */
    public function byPosicao($posicao_id)
    {
        return $this->innerJoinWith(['posicao'])->andWhere(['banner.posicao_id' => $posicao_id]);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $subcategoria_id
     * @return $this
     */
    public function bySubCategoria($subcategoria_id)
    {
        if (is_null($subcategoria_id)) {
            return $this;
        }

        return $this->innerJoinWith(['subcategorias'])->andWhere(['subcategoria.id' => $subcategoria_id]);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $produto_id
     * @return $this
     */
    public function byProduto($produto_id)
    {
        if (is_null($produto_id)) {
            return $this;
        }

        return $this->innerJoinWith(['produto'])->andWhere(['produto.id' => $produto_id]);
    }
}
