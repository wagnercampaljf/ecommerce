<?php
//1111
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
 * @property resource $imagem_zoom
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
                ['imagem', 'imagem_sem_logo', 'imagem_zoom'],
                'image',
                'extensions' => 'png, jpg, gif, jpf, webp',
                'maxSize' => 50000000,
            ],

            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']]
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
            'imagem_zoom' => 'Imagem Zoom',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otávio 17/08/2017
     */

    public function afterSave($insert, $changedAttributes)
    {
        $stream_opts = [
            "ssl" => [
                "verify_peer" => false,
            ]
        ];

        $imagem = Imagens::find()->andWhere(["=", "id", $this->id])->one();

        if (!empty($imagem)) {

            if (!empty($imagem->imagem)) {

                if (!file_exists('/var/www/imagens_produto/produto_' . $imagem['produto_id'])) {
                    mkdir('/var/www/imagens_produto/produto_' . $imagem['produto_id'], 0777, true);

                    $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                    $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . ".webp";
                    copy($caminho, $destino, stream_context_create($stream_opts));

                    if ($imagem['imagem_sem_logo'] !== null) {
                        $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                        $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_sem_logo.webp";
                        copy($caminho, $destino, stream_context_create($stream_opts));
                    }

                    if ($imagem['imagem_zoom'] !== null) {
                        $caminho = "https://www.pecaagora.com/site/get-link-zoom?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                        $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_zoom.webp";
                        copy($caminho, $destino, stream_context_create($stream_opts));
                    }
                } else {

                    if ($insert) {
                        if ($imagem->imagem) {
                            $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                            $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . ".webp";
                            copy($caminho, $destino, stream_context_create($stream_opts));
                        }

                        if ($imagem->imagem_sem_logo) {
                            $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                            $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . "_sem_logo.webp";
                            copy($caminho, $destino, stream_context_create($stream_opts));
                        }
                    } else {

                        if (!empty($changedAttributes['imagem'])) {

                            if (!empty($changedAttributes['ordem'])) {

                                $arquivo_antigo = '/var/www/imagens_produto/produto_' . $this->produto_id . '/' . $this->produto_id . '_' . $changedAttributes['ordem'] . '.webp';
                                $arquivo_novo = '/var/www/imagens_produto/produto_' . $this->produto_id . '/' . $this->produto_id . '_' . $this->ordem . '.webp';
                                rename($arquivo_antigo, $arquivo_novo);

                                $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . ".webp";
                                copy($caminho, $destino, stream_context_create($stream_opts));
                            } else {

                                $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . ".webp";
                                copy($caminho, $destino, stream_context_create($stream_opts));
                            }
                        }
                        if (!empty($changedAttributes['imagem_sem_logo'])) {

                            if (!empty($changedAttributes['ordem'])) {

                                $arquivo_antigo = '/var/www/imagens_produto/produto_' . $this->produto_id . '/' . $this->produto_id . '_' . $changedAttributes['ordem'] . '_sem_logo.webp';
                                $arquivo_novo = '/var/www/imagens_produto/produto_' . $this->produto_id . '/' . $this->produto_id . '_' . $this->ordem . '_sem_logo.webp';
                                rename($arquivo_antigo, $arquivo_novo);

                                $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . "_sem_logo.webp";
                                copy($caminho, $destino, stream_context_create($stream_opts));
                            } else {

                                $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $this->produto_id . '&' . 'ordem=' . $this->ordem;
                                $destino = '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $this->produto_id . '_' . $this->ordem . "_sem_logo.webp";
                                copy($caminho, $destino, stream_context_create($stream_opts));
                            }
                        }

                        if (!empty($changedAttributes['ordem']) && $this->ordem !== $changedAttributes['ordem']) {

                            if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp')) {

                                $arquivo_antigo = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp';
                                $arquivo_novo   = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $this->ordem . '_sem_logo.webp';
                                rename($arquivo_antigo, $arquivo_novo);
                            }
                            if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp')) {

                                $arquivo_antigo = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp';
                                $arquivo_novo   = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $this->ordem . '.webp';
                                rename($arquivo_antigo, $arquivo_novo);
                            }
                        }
                    }
                }
            } else {
                if (!empty($changedAttributes['ordem']) && $this->ordem !== $changedAttributes['ordem']) {

                    if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp')) {

                        $arquivo_antigo = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '_sem_logo.webp';
                        $arquivo_novo   = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $this->ordem . '_sem_logo.webp';
                        rename($arquivo_antigo, $arquivo_novo);
                    }
                    if (file_exists('/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp')) {

                        $arquivo_antigo = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $changedAttributes['ordem'] . '.webp';
                        $arquivo_novo   = '/var/www/imagens_produto/produto_' . $changedAttributes['produto_id'] . '/' . $changedAttributes['produto_id'] . '_' . $this->ordem . '.webp';
                        rename($arquivo_antigo, $arquivo_novo);
                    }
                }
            }
        }
    }

    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
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

    public function byCodFabricanteOrdem($cod_fabricante)
    {
        return $this->joinWith('produto')
            ->andWhere(['produto.codigo_fabricante' => $cod_fabricante])
            ->andWhere(['IS NOT', 'imagens.imagem_sem_logo', null]);
    }

    public function ImagemReferencia($model, $method = null)
    {
        if ($method == 'delete') {
            unlink('/var/www/html/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '_sem_logo.webp');
            unlink('/var/www/html/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '.webp');
        } else {

            if (!file_exists('/var/www/html/imagens_produto/produto_' . $model['produto_id'])) {
                mkdir('/var/www/html/imagens_produto/produto_' . $model['produto_id'], 0777, true);
            }

            if (!empty($model)) {

                $caminho = "http://localhost/pecaagora/site/get-link?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
                copy($caminho, '/var/www/html/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . ".webp");

                if ($model->imagem_sem_logo !== null) {
                    $caminho = "http://localhost/pecaagora/site/get-link-sem-logo?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
                    copy($caminho, '/var/www/html/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . "_sem_logo.webp");
                }

                if ($model->imagem_zoom !== null) {
                    $caminho = "http://localhost/pecaagora/site/get-link-zoom?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
                    copy($caminho, '/var/www/html/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . "_zoom.webp");
                }
            }
        }
    }
}
