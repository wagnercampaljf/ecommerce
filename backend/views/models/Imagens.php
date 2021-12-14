<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Este é o model para a tabela "imagens".
 *
 * @property integer $id
 * @property integer $produto_id
 * @property resource $imagem
 * @property resource $imagem_sem_logo
 * @property integer $ordem
 *
 * @property Produto $produto
 *
 * @author Otávio 17/08/2017
 */
class Imagens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Otávio 17/08/2017
     */
    public static function tableName()
    {
        return 'imagens';
    }

    /**
     * @inheritdoc
     * @author Otávio 17/08/2017
     */
    public function rules()
    {
        return [
            [['produto_id', 'ordem'], 'required'],
            [['id', 'produto_id', 'ordem'], 'integer'],
            [['imagem'], 'required', 'on' => ['create']],
            [
                ['imagem', 'imagem_sem_logo'],
                'image',
                'extensions' => 'png, jpg, gif, webp',
                'maxSize' => 500000,
            ],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Otávio 17/08/2017
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_id' => 'Produto ID',
            'imagem' => 'Imagem',
            'ordem' => 'Posição',
            'imagem_sem_logo' => 'Imagem Sem Logo',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $imagem = Imagens::find()->andWhere(["=", "id", $this->id])->one();

        if (!empty($imagem)) {

            if (!file_exists('/var/www/imagens_produto/produto_' . $imagem['produto_id'])) {
                mkdir('/var/www/imagens_produto/produto_' . $imagem['produto_id'], 0777, true);
            } else {
                
                if ($changedAttributes['ordem'] != null && $this->ordem !== $changedAttributes['ordem']) {
                    if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp')) {
                        unlink('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp');
                    }
                    if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp')) {
                        unlink('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp');
                    }
                }
            }

            $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
            $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . ".webp";
            copy($caminho, $destino);
            // chmod($destino, 0777);

            if ($imagem['imagem_sem_logo'] !== null) {
                $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_sem_logo.webp";
                copy($caminho, $destino);
                // chmod($destino, 0777);
            }

            if ($imagem['imagem_zoom'] !== null) {
                $caminho = "https://www.pecaagora.com/site/get-link-zoom?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_zoom.webp";
                copy($caminho, $destino);
                // chmod($destino, 0777);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 17/08/2017
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

    public function getImg($model, $options = [], $logo = true)
    {
        if (!empty($model)) {

            if ($logo) {
                $src = '/imagens/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '.webp';
            } else {
                $src = '/imagens/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '_sem_logo.webp';
            }
        } else {
            $src = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return Html::img($src, $options);
    }

//     public function getImg($options, $logo = true)
//     {
//         $imagem = ($logo) ? $this->imagem : $this->imagem_sem_logo;
// //        $imagem = $this->imagem;
//         if (is_string($imagem)) {
//             $options = ArrayHelper::merge(
//                 ['width' => '160', 'heigth' => '160'],
//                 $options
//             );

//             return Html::img('data:image;base64,' . $imagem, $options);
//         }
//         if ($imagem) {
//             $options = ArrayHelper::merge(
//                 ['width' => '160', 'heigth' => '160'],
//                 $options
//             );

//             return Html::img('data:image;base64,' . stream_get_contents($imagem), $options);
//         }
//         $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
//         return Html::img($src, $options);
//     }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 17/08/2017
     */
    public static function find()
    {
        return new ImagensQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Imagens, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Otávio 17/08/2017
 */
class ImagensQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Otávio 17/08/2017
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['imagens.nome' => $sort_type]);
    }

    public function byCodFabricante($cod_fabricante)
    {
        return $this->joinWith('produto')->andWhere(['produto.codigo_fabricante' => $cod_fabricante]);
    }

    public function byCodGlobal($cod_global)
    {
        return $this->joinWith('produto')->andWhere(['produto.codigo_global' => $cod_global]);
    }

    public function byCodFabricanteOrdem($cod_fabricante)
    {
        return $this->joinWith('produto')
                    ->andWhere(['produto.codigo_fabricante' => $cod_fabricante])
                    ->andWhere(['IS NOT','imagens.imagem_sem_logo', null]);
    }
}
