<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "categoria_banner".
 *
 * @property integer $id
 * @property string $nome
 * @property string $slug
 *
 * @property Banner[] $banners
 *
 * @author Igor 06/10/2015
 */
class CategoriaBanner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public static function tableName()
    {
        return 'categoria_banner';
    }

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'slug'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 50]
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
            'nome' => Yii::t('app', 'Nome'),
            'slug' => Yii::t('app', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 22/04/2016
     */
    public function getBannerCategoriaBanners()
    {
        return $this->hasMany(BannerCategoriaBanner::className(), ['categoria_banner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 22/04/2016
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['id' => 'banner_id'])->viaTable('banner_categoria_banner', ['categoria_banner_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public static function find()
    {
        return new CategoriaBannerQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da CategoriaBanner, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 06/10/2015
 */
class CategoriaBannerQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['categoria_banner.nome' => $sort_type]);
    }
}
