<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use common\models\Imagens;
use Livepixel\MercadoLivre\Meli;

/**
 * Este é o model para a tabela "produto".
 *
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 * @property string $localizacao
 * @property double $peso
 * @property double $altura
 * @property double $largura
 * @property double $profundidade
 * @property resource $imagem
 * @property resource $images
 * @property resource $imagemSemLogo
 * @property string $codigo_global
 * @property string $codigo_montadora
 * @property string $codigo_fabricante
 * @property string $codigo_fornecedor
 * @property integer $fabricante_id
 * @property string $slug
 * @property string $micro_descricao
 * @property integer $subcategoria_id
 * @property string $aplicacao
 * @property string $codigo_similar
 * @property string $aplicacao_complementar
 * @property string $texto_vetor
 * @property integer $multiplicador
 * @property string $video
 * @property string $codigo_barras
 * @property string $cesp
 * @property double $ipi
 * @property integer $filial_id
 * @property integer quantidade
 * @property integer status_b2w
 * @property integer envio
 * @property integer ordem
 * @property integer $produto_condicao_id
 * @property integer $marca_produto_id
 * @property integer $dias_expedicao

 * @property resource $imagem_sem_logo
 * @property resource $imagem_zoom
 * @property boolean $e_usado
 * @property boolean $e_medidas_conferidas
 * @property boolean $e_ativo
 * @property boolean $e_valor_bloqueado
 * @property boolean $e_localizacao_bloqueado
 *
 * @property ProdutoFilial[] $produtoFilials
 * @property Fabricante $fabricante
 * @property Filial $filial
 * @property Subcategoria $subcategoria
 * @property ProdutoCondicao $produtoCondicao
 * @property MarcaProduto $marcaProduto
 * @property ProdutoAnoModelo[] $produtoAnoModelos
 * @property ProdutoModelo[] $produtoModelo
 * @property AnoModelo[] $anoModelos
 * @property Visita[] $visitas
 * @property ProdutoAtributo[] $produtoAtributos
 * @property Banner[] $banners
 * @property Imagens[] $imagens
 *
 * @author Vinicius Schettino 03/12/2014
 */
class Produto extends \yii\db\ActiveRecord
{
    public $nome_search;
    public $codigo_search;
    public $aplicacao_search;
    public $complementar_search;
    public $similar_search;
    public $anoModelo_id;
    public $menor_preco = 123345567;
    public $filial_id;
    public $quantidade;
    public $status_b2w;
    public $envio;
    public $valor;
    public $valor_cnpj;
    public $valor_compra;
    public $promocao;
    public $ordem;
    public $imagem;
    public $imagem_sem_logo;
    public $imagem_zoom;

    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    /*public $marcaProduto;*/


    /**
     * @inheritdoc
     * @author Vinicius Schettino 03/12/2014
     */
    public static function tableName()
    {
        return 'produto';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 03/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'codigo_global', 'subcategoria_id', 'produto_condicao_id', 'fabricante_id'], 'required'],
            [['descricao', 'localizacao', 'codigo_similar', 'aplicacao_complementar', 'aplicacao', 'video', 'codigo_barras', 'cest'], 'string'],
            [['peso', 'altura', 'largura', 'profundidade', 'ipi', 'valor', 'valor_cnpj', 'valor_compra'], 'number'],
            [['fabricante_id', 'subcategoria_id', 'marca_produto_id', 'quantidade', 'multiplicador', 'envio', 'ordem', 'filial_id', 'dias_expedicao'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['codigo_global', 'codigo_montadora', 'codigo_fabricante', 'codigo_fornecedor'], 'string', 'max' => 50],
            ['codigo_global', 'unique'],
            [['slug'], 'string', 'max' => 200],
            [['micro_descricao'], 'string', 'max' => 250],
            [['anoModelo_id', 'e_usado', 'e_medidas_conferidas', 'e_ativo', 'e_localizacao_bloqueado', 'e_valor_bloqueado'], 'safe'],
            [['status_b2w', 'promocao'], 'boolean'],
            [['imagem'], 'required', 'on' => ['create']],
            [['filial_id', 'valor'], 'required', 'on' => ['create']],
            [
                ['imagem', 'imagem_sem_logo', 'imagem_zoom'],
                'image',
                'extensions' => 'png, jpg, gif, jpf, webp',
                'maxSize' => 50000000,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 03/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id'                        => 'ID',
            'nome'                      => 'Nome',
            'descricao'                 => 'Descrição',
            'localizacao'               => 'Localização',
            'peso'                      => 'Peso',
            'altura'                    => 'Altura',
            'largura'                   => 'Largura',
            'profundidade'              => 'Profundidade',
            'codigo_global'             => 'Código Global',
            'codigo_montadora'          => 'Código Montadora',
            'codigo_fabricante'         => 'Código Fabricante',
            'codigo_fornecedor'         => 'Código Fornecedor',
            'fabricante_id'             => 'Fabricante',
            'slug'                      => 'Slug',
            'micro_descricao'           => 'Micro Descricao',
            'subcategoria_id'           => 'Subcategoria',
            'codigo_similar'            => 'Códigos Similares',
            'aplicacao_complementar'    => 'Aplicação Complementar',
            'aplicacao'                 => 'Aplicação',
            'multiplicador'             => 'Multiplicador',
            'video'                     => 'Vídeo',
            'codigo_barras'             => 'Código de Barras',
            'cest'                      => 'CEST',
            'ipi'                       => 'IPI',
            'filial_id'                 => 'Filial',
            'quantidade'                => 'Quantidade',
            'status_b2w'                => 'Status B2W',
            'envio'                     => 'Envio',
            'valor'                     => 'Valor',
            'valor_cnpj'                => 'Valor Cnpj',
            'valor_compra'        => 'Valor Compra',
            'promocao'                  => 'Promoção',
            'imagem'                    => 'Imagem',
            'ordem'                     => 'Posição',
            'imagem_sem_logo'           => 'Imagem Sem Logo',
            'imagem_zoom'               => 'Imagem Zoom',
            'e_usado'            => 'É usado?',
            'e_medidas_conferidas'    => 'Medidas Conferidas',
            'e_ativo'            => 'É ativo?',
            'e_valor_bloqueado'    => 'É valor bloqueado?',
            'e_localizacao_bloqueado'    => 'É Localização bloqueada',
            'produto_condicao_id' => 'Condição',
            'dias_expedicao' => 'Dias de Expedição'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getFabricante()
    {
        return $this->hasOne(Fabricante::className(), ['id' => 'fabricante_id']);
    }

    public function getProdutoCondicao()
    {
        return $this->hasOne(ProdutoCondicao::className(), ['id' => 'produto_condicao_id']);
    }

    public function getMarcaProduto()
    {
        return $this->hasOne(MarcaProduto::className(), ['id' => 'marca_produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::className(), ['id' => 'subcategoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getAtributosProduto()
    {
        return $this->hasMany(ProdutoAtributo::className(), ['produto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getFiliaisProduto()
    {
        return $this->hasMany(ProdutoFilial::className(), ['produto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getVisitas()
    {
        return $this->hasMany(Visita::className(), ['produto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getProdutoAnosModelo()
    {
        return $this->hasMany(ProdutoAnoModelo::className(), ['produto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function getAnosModelo()
    {
        return $this->hasMany(AnoModelo::className(), ['id' => 'ano_modelo_id'])->viaTable(
            'produto_ano_modelo',
            ['produto_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 04/11/2016
     */
    public function getProdutoModelo()
    {
        if (empty($this->id)) {
            $row = [];
            return $row;
        }
        $query = new Query;
        $row = $query
            ->addSelect("am.id")
            ->addSelect(["CONCAT(mo.nome,' ',am.nome) AS nome"])
            ->from("modelo mo")
            ->innerJoin("ano_modelo am", "am.modelo_id = mo.id")
            ->innerJoin("produto_ano_modelo pam", "am.id = pam.ano_modelo_id")
            ->where("pam.produto_id = $this->id")
            ->orderBy("mo.nome ASC")
            ->all();

        return $row;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor Mageste 09/10/2015
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['produto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public static function find()
    {
        return new ProdutoQuery(get_called_class());
    }

    /**
     * label do produto formada pelo nome + o código encapsulado por parenteses
     * @author Vinicius Schettino 03/12/2014
     */
    public function getLiabelSearch()
    {
        $nome = empty($this->nome_search) ? $this->nome : $this->nome_search;
        $codigo = empty($this->codigo_search) ? $this->codigo_global : $this->codigo_search;

        return $nome . ' (' . $codigo . ')';
    }

    public function getLabel()
    {
        $nome = $this->nome;
        $codigo = empty($this->codigo_search) ? $this->codigo_global : $this->codigo_search;

        //        return $nome . "<span class='codigo-nome'> (" . $codigo . ")</span>";
        return $nome . " (" . $codigo . ")";
    }


    public function getAplicacaoSearch()
    {
        return empty($this->aplicacao_search) ? $this->aplicacao : $this->aplicacao_search;
    }

    /**
     * wrapper do código global com a # no início
     * @author Vinicius Schettino 03/12/2014
     * Alteração: não estamos mais utilizando o # então essa concatenação foi removida
     * @author Otavio Augusto 17/08/2016
     */
    public function getCodigo()
    {
        return $this->codigo_global;
    }

    /**
     * método mágico retornando a label do produto
     * @author Vinicius Schettino 03/12/2014
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * Micro Descrição do produto, representado pela @property micro_descricao. se esta
     * estiver vazia, retorna os 250 primeiros caracteres da descrição com "..." no final.
     * @author Vinicius Schettino 03/12/2014
     */
    public function microDescricao()
    {
        if (!is_null($this->micro_descricao)) {
            return $this->micro_descricao;
        }

        return StringHelper::truncate($this->descricao, 250);
    }

    public function getUrl($absolute = true)
    {
        $slug = '';
        if (!empty($this->slug)) {
            $slug = '/' . $this->slug;
        }
        $src = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/p/' . $this->id . $slug;
        //        echo Yii::$app->urlManagerFrontEnd->baseUrl.'/p/' . $this->id . $slug;
        //        echo Url::to(['/p/' . $this->id . '/' . $this->slug,], $absolute);
        return $src;
    }

    /**
     * #############################   Multi Imagens
     * @return \yii\db\ActiveQuery
     */


    public function getImagens()
    {
        return $this->hasMany(Imagens::className(), ['produto_id' => 'id'])->orderBy(['ordem' => SORT_ASC]);
    }


    /**
     * @author Igor Mageste
     * @since 29/06/2016
     * @return string
     */
    public function getUrlImageML()
    {
        $imagens = $this->imagens;

        //$imagens = ArrayHelper::index($imagens,'ordem');
        $ordem = $imagens->ordem;

        if (!empty($imagens)) {
            //$src = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->id . "&ordem=1";
            $src = "http://31.220.57.2/site/get-link?produto_id=" . $this->id . "&ordem=1";
        } else {
            $src = "https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png";
        }

        return $src;
    }

    public function getUrlImageBackend()
    {
        $imagem = Imagens::find()->andWhere(['=', 'produto_id', $this->id])->orderBy('ordem')->one();

        if ($imagem) {
            $src = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->id . "&ordem=" . $imagem->ordem;
        } else {
            $src = "https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png";
        }

        return $src;
    }

    public function getUrlImagesML()
    {
        $src = array();
        $imagens = $this->imagens;

        ArrayHelper::multisort($imagens, ['ordem'], [SORT_ASC]);

        if (!empty($imagens)) {
            foreach ($imagens as $k => $v) {
                //$src[] = ['source' => 'https://www.pecaagora.com/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem];
                //12-02-2020$src[] = ['source' => 'http://31.220.57.2/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem ]; //Imagem com logo
                //$src[] = ['source' => 'http://31.220.57.2/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $v->ordem]; //Imagem sem logo

                $caminho    = "https://www.pecaagora.com/site/get-link?produto_id=" . $v->produto_id . "&ordem=" . $v->ordem;
                copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/' . $v->id . ".webp");
                $resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg ' . $v->id . '.webp');
                $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/' . $v->id . '.jpg'];
            }
        } else {
            $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png'];
        }
        return $src;
    }



    public function getUrlImagesMLSemLogo()
    {
        /*$src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            foreach ($imagens as $k => $v) {
                $src[] = ['source' => 'https://www.pecaagora.com/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $v->ordem];
            }
        } else {
            $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png'];
        }
        return $src;*/

        $src = array();
        $imagens = $this->imagens;

        ArrayHelper::multisort($imagens, ['ordem'], [SORT_ASC]);

        if (!empty($imagens)) {
            foreach ($imagens as $k => $v) {
                //$src[] = ['source' => 'https://www.pecaagora.com/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem];
                //12-02-2020$src[] = ['source' => 'http://31.220.57.2/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem ]; //Imagem com logo
                //$src[] = ['source' => 'http://31.220.57.2/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $v->ordem]; //Imagem sem logo

                $caminho    = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $v->produto_id . "&ordem=" . $v->ordem;
                copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/' . $v->id . ".webp");
                $resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg ' . $v->id . '.webp');
                $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/' . $v->id . '.jpg'];
            }
        } else {
            $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png'];
        }
        return $src;
    }


    /**
     * @author Otavio Augusto
     * @since 21/08/2017
     * @return array
     */
    public function getUrlImagesSkyhub()
    {
        $src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            foreach ($imagens as $imagem) {
                if ($imagem->imagem_sem_logo) {
                    $src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
                }
            }
        } else {
            $src = $imagens;
        }

        return $src;
    }

    public function getUrlImagesB2WComlogo()
    {
        $src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            foreach ($imagens as $k => $imagem) {
                if ($k == 0) {
                    //$src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
                    $src[] = str_replace('http://', 'https://www.', Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem . '&b2w.jpg');
                } else {
                    //$src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
                    $src[] = str_replace('http://', 'https://www.', Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem . '&b2w.jpg');
                }
            }
        } else {
            $src[] = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return $src;
    }

    public function getUrlImagesB2WWebp()
    {
        $src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            foreach ($imagens as $k => $imagem) {
                $caminho    = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $imagem->produto_id . "&ordem=" . $imagem->ordem;
                copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/' . $imagem->id . "_sem_logo.webp");
                $resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg ' . $imagem->id . '_sem_logo.webp');
                $src[] = 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/' . $imagem->id . '_sem_logo.jpg';
            }
        } else {
            $src[] = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return $src;
    }

    /**
     * @author Igor Mageste
     * @since 29/06/2016
     * @return string
     */
    public function getUrlImage($logo = true)
    {
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            ArrayHelper::multisort($imagens, ['ordem'], [SORT_ASC]);
            $ordem = ArrayHelper::getValue($imagens, '0.ordem');

            if ($logo) {
                $src = '/imagens/produto_' . $this->id . '/' . $this->id . '_' . $ordem . '.webp';
            } else {
                $src = '/imagens/produto_' . $this->id . '/' . $this->id . '_' . $ordem . '_sem_logo.webp';
            }

            // if ($logo) {
            //     $src = Url::base(true) . '/site/get-link?produto_id=' . $this->id . '&ordem='.$ordem;
            // } else {
            //     $src = Url::base(true) . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem='.$ordem;
            // }
        } else {
            $src = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return $src;
    }

    /**
     * @author Otavio Augusto
     * @since 21/08/2017
     * @return array
     */
    public function getUrlImages($logo = true)
    {
        $src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            if ($logo) {
                foreach ($imagens as $imagem) {
                    $src[] = Url::base(true) . '/imagens/produto_' . $this->id . '/' . $this->id . '_' . $imagem->ordem . '.webp';
                }
            } else {
                foreach ($imagens as $imagem) {
                    $src[] = Url::base(true) . '/imagens/produto_' . $this->id . '/' . $this->id . '_' . $imagem->ordem . '_sem_logo.webp';
                }
            }
        } else {
            $src[] = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return $src;


        // if (!empty($imagens)) {
        //     if ($logo) {
        //         foreach ($imagens as $imagem) {
        //             $src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
        //         }
        //     } else {
        //         foreach ($imagens as $imagem) {
        //             $src[] = str_replace('http://', 'https://www.', Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem . '&b2w.jpg');
        //         }
        //     }
        // } else {
        //     $src[] = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        // }

        // return $src;
    }

    /**
     * Retorna o html da imagem do produto
     * @author Otavio Augusto
     * @param array $options Opções para a tag img
     * @return Array de objeto imagens
     */

    public function getImages()
    {
        $src = $this->getUrlImages();
        return $src;
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
     * Retorna o html da imagem sem logo do produto
     * @author Igor Mageste
     * @param array $options Opções para a tag img
     * @return string
     */
    public function getImagemSemLogo($options = [])
    {
        $src = $this->getUrlImage(false);

        return Html::img($src, $options);
    }

    public function getImgs($options, $logo = true)
    {
        $imagem = ($logo) ? $this->imagem : $this->imagem_sem_logo;
        if (is_string($imagem)) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . $imagem, $options);
        }
        if ($imagem) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . stream_get_contents($imagem), $options);
        }
        $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
        return Html::img($src, $options);
    }

    public function getImg($options, $logo = true)
    {
        $imagem = ($logo) ? $this->imagem : $this->imagem_sem_logo;
        if (is_string($imagem)) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . $imagem, $options);
        }
        if ($imagem) {
            $options = ArrayHelper::merge(
                ['width' => '250', 'heigth' => '250'],
                $options
            );

            return Html::img('data:image;base64,' . stream_get_contents($imagem), $options);
        }
        $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
        return Html::img($src, $options);
    }

    public function atualizarMercadoLivre($model, $atributo = null)
    {
        $retorno = array();

        if ($atributo) {
            switch ($atributo) {
                case 'nome':
                    array_push($this->atualizarNome($model), $retorno);
                    break;
                case 'video':
                    array_push($this->atualizarCondicao($model), $retorno);
                    break;
                case 'condicao':
                    array_push($this->atualizarVideo($model), $retorno);
                    break;
                case 'marca':
                    array_push($this->atualizarMarca($model), $retorno);
                    break;
                case 'codigo':
                    array_push($this->atualizarCodigo($model), $retorno);
                    break;
                case 'categoria':
                    array_push($this->atualizarCategoria($model), $retorno);
                    break;
                case 'envio':
                    array_push($this->atualizarDescricao($model), $retorno);
                    break;
                case 'imagens':
                    array_push($this->atualizarModoEnvio($model), $retorno);
                    break;
                case 'descricao':
                    array_push($this->atualizarImagens($model), $retorno);
                    break;
            }
        } else {


            $return = $this->atualizarNome($model);
            array_push($retorno, $return);
            $return = $this->atualizarCondicao($model);
            array_push($retorno, $return);
            $return = $this->atualizarVideo($model);
            array_push($retorno, $return);
            $return = $this->atualizarMarca($model);
            array_push($retorno, $return);
            $return = $this->atualizarCodigo($model);
            array_push($retorno, $return);
            $return = $this->atualizarCategoria($model);
            array_push($retorno, $return);
            $return = $this->atualizarDescricao($model);
            array_push($retorno, $return);
        }

        return $retorno;
    }

    public static function atualizar($model, $body, $origem)
    {
        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])->orderBy(["filial_id" => SORT_ASC])->all();

        $retorno = array();

        foreach ($produtos_filiais as $j => $produto_filial) {

            $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;

            if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
                echo " - Filial fora do ML";
                continue;
            }

            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
            $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');
            $meliAccessToken    = $response->access_token;

            if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
                $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
                $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_status"] = "$origem não alterado";
                    echo "\nNome não alterado (MELI_ID)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_status"] = "$origem alterado";
                    echo "\nNome alterado (MELI_ID)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
                $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "$origem não alterado";
                    echo "\nNome não alterado (MELI_ID_SEM_JUROS)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "$origem alterado";
                    echo "\nNome alterado (MELI_ID_SEM_JUROS)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
                $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "$origem não alterado";
                    echo "\nNome não alterado (MELI_ID_FULL)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "$origem alterado";
                    echo "\nNome alterado (MELI_ID_FULL)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
                $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
                $response = $meli->put("items/{$produto_filial->meli_id_flex}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "$origem não alterado";
                    echo "\nNome não alterado (MELI_ID_FLEX)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "$origem alterado";
                    echo "\nNome alterado (MELI_ID_FLEX)" . $response["body"]->permalink;
                }
            }
        }

        return $retorno;
    }

    public function atualizarNome($model)
    {
        $body = [
            'attributes' => [
                [
                    "id" => "PART_NUMBER",
                    "name" => "Número de peça",
                    "value_id" => null,
                    "value_name" => $model->codigo_global,
                    "value_struct" => null,
                    "attribute_group_id" => "OTHERS",
                    "attribute_group_name" => "Outros"
                ],
            ]
        ];

        return self::atualizar($model, $body, 'Codigo');
    }

    public function atualizarCodigo($model)
    {
        $title = Yii::t('app', '{nome}', ['nome' => $model->nome]);
        $title = str_replace("IVECO", "", str_replace("Iveco", "", $title));
        $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];

        return self::atualizar($model, $body, 'Nome');
    }

    public function atualizarCondicao($model)
    {
        $condicao = "new";
        $condicao_id = "2230284";
        $condicao_name = "Novo";
        if ($model->produtoCondicao) {
            switch ($model->produtoCondicao->meli_id) {
                case "new":
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    break;
                case "used":
                    $condicao = "used";
                    $condicao_id = "2230581";
                    $condicao_name = "Usado";
                    break;
                case "recondicionado":
                    $condicao = "new";
                    $condicao_id = "2230582";
                    $condicao_name = "Recondicionado";
                    break;
                default:
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
            }
        };

        $body = [
            "condition" => $condicao,
            'attributes' => [
                [
                    'id'                    => 'ITEM_CONDITION',
                    'name'                  => 'Condição do item',
                    'value_id'              => $condicao_id,
                    'value_name'            => $condicao_name,
                    'value_struct'          => null,
                    'values'                => [[
                        'id'    => $condicao_id,
                        'name'  => $condicao_name,
                        'struct' => null,
                    ]],
                    'attribute_group_id'    => "OTHERS",
                    'attribute_group_name'  => "Outros"
                ]
            ]
        ];


        return self::atualizar($model, $body, 'Condição');
    }

    public function atualizarModoEnvio($model)
    {
        $body = [
            "shipping"      => [
                "mode"          => "me2",
                "methods"       => [],
                "local_pick_up" => true,
                "free_shipping" => false,
                "logistic_type" => "cross_docking",
            ],
        ];

        return self::atualizar($model, $body, 'Modo Envio');
    }

    public function atualizarImagens($model)
    {
        $body = [
            "pictures" => $model->getUrlImagesML(),
        ];

        return self::atualizar($model, $body, 'Imagens');
    }

    public function atualizarDescricao($model)
    {
        $page = $model->nome . "\n\nAPLICAÇÃO:\n\n" . $model->aplicacao . $model->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo é o do Motorista.\n\n* Lado Direito é o do Passageiro.";
        $page = str_replace("'", "", $page);
        $page = str_replace("<p>", " ", $page);
        $page = str_replace("</p>", " ", $page);
        $page = str_replace("<br>", "\n", $page);
        $page = str_replace("<BR>", "\n", $page);
        $page = str_replace("<br/>", "\n", $page);
        $page = str_replace("<BR/>", "\n", $page);
        $page = str_replace("<strong>", " ", $page);
        $page = str_replace("</strong>", " ", $page);
        $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
        $page = str_replace('</span>', " ", $page);
        $page = str_replace('<span>', " ", $page);
        $page = str_replace('<ul>', " ", $page);
        $page = str_replace('</ul>', " ", $page);
        $page = str_replace('<li>', "\n", $page);
        $page = str_replace('</li>', " ", $page);
        $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
        $page = str_replace('<h1>', " ", $page);
        $page = str_replace('</h1>', " ", $page);
        $page = str_replace('<h2>', " ", $page);
        $page = str_replace('</h2>', " ", $page);
        $page = str_replace('<h3>', " ", $page);
        $gege = str_replace('</h3>', " ", $page);
        $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
        $page = str_replace('>>>', "(", $page);
        $page = str_replace('<<<', ")", $page);
        $page = str_replace('<u>', " ", $page);
        $page = str_replace('</u>', "\n", $page);
        $page = str_replace('<b>', " ", $page);
        $page = str_replace('</b>', " ", $page);
        $page = str_replace('<o:p>', " ", $page);
        $page = str_replace('</o:p>', " ", $page);
        $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
        $page = str_replace('<del>', " ", $page);
        $page = str_replace('</del>', " ", $page);
        $page = str_replace('/', "-", $page);
        $page = str_replace('<em>', " ", $page);
        $page = str_replace('<-em>', " ", $page);

        $body = ['plain_text' => $page];

        return self::atualizar($model, $body, 'Descrição');
    }

    public function atualizarMarca($model)
    {
        $marca_produto = MarcaProduto::find()->andWhere(['=', 'id', $model->marca_produto_id])->one();

        if ($marca_produto) {
            $body = [
                'attributes' => [
                    [
                        "id" => "BRAND",
                        "name" => "Marca",
                        "value_id" => null,
                        "value_name" => $marca_produto->nome,
                        "value_struct" => null,
                        "attribute_group_id" => "OTHERS",
                        "attribute_group_name" => "Outros"
                    ],
                ]
            ];
        }

        return self::atualizar($model, $body, 'Marca');
    }

    public function atualizarCategoria($model)
    {
        $body = [
            "category_id" => utf8_encode($model->subcategoria->meli_id),
        ];

        return self::atualizar($model, $body, 'Categoria');
    }

    public function atualizarVideo($model)
    {
        $video_id = "";
        if ($model->video == null) {
            $video_id = "rqvlr169tfE";
        } else {
            $video_complemento  = explode("=", $model->video);
            if (isset($video_complemento[1])) {
                $video_codigo       = explode("&", $video_complemento[1]);
                $video_id           = $video_codigo[0];
            } else {
                $retorno[$model->id]["meli_id_status"] = "Video não encontrado";
                return $retorno;
            }
        }

        $body = [
            "video_id" => $video_id,
        ];

        return self::atualizar($model, $body, 'Video');
    }


    function atualizarProdutoML($meli, $token, $meli_id, $preco, $quantidade, $modo = "me2", $meli_origem)
    {

        $mensagem_retorno   = '<div class="h4">MELI ID: ' . $meli_id . '</div>';
        $link               = "";


        //Atualização da Condição
        $condicao = "new";
        $condicao_id = "2230284";
        $condicao_name = "Novo";
        if ($this->produtoCondicao) {
            switch ($this->produtoCondicao->meli_id) {
                case "new":
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    break;
                case "used":
                    $condicao = "used";
                    $condicao_id = "2230581";
                    $condicao_name = "Usado";
                    break;
                case "recondicionado":
                    $condicao = "new";
                    $condicao_id = "2230582";
                    $condicao_name = "Recondicionado";
                    break;
                default:
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
            }
        };

        $body = [
            "condition" => $condicao,
            'attributes' => [
                [
                    'id'                    => 'ITEM_CONDITION',
                    'name'                  => 'Condição do item',
                    'value_id'              => $condicao_id,
                    'value_name'            => $condicao_name,
                    'value_struct'          => null,
                    'values'                => [[
                        'id'    => $condicao_id,
                        'name'  => $condicao_name,
                        'struct' => null,
                    ]],
                    'attribute_group_id'    => "OTHERS",
                    'attribute_group_name'  => "Outros"
                ]
            ]
        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Condição não atualizada no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Condição atualizada no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }

        //Atualização Modo de Envio
        $body = [
            /*"shipping" => [
                "mode" => $modo,
                "local_pick_up" => true,
                "free_shipping" => false,
                "free_methods" => [],
            ],*/
            "shipping"      => [
                "mode"          => "me2",
                "methods"       => [],
                "local_pick_up" => true,
                "free_shipping" => false,
                "logistic_type" => "cross_docking",
            ],
        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Modo não atualizado no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Modo atualizado no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }

        //Atualização Quantidade
        if ($quantidade == 0) {
            $body = [
                "status"                => "paused"
            ];
            $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
            if ($response['httpCode'] >= 300) {
                $mensagem_retorno   .= '<div class="text-danger h4">Quantidade n  o atualizada no Mercado Livre</div>';
                echo "<pre>";
                print_r($response);
                echo "</pre>";
                //die;
            } else {
                $mensagem_retorno   .= '<div class="text-success h4">Quantidade atualizada no Mercado Livre (' . $quantidade . ' unidades)</div>';
                $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
            }
        } else {
            $body = [
                "available_quantity"     => $quantidade,
                "status"        => "active"
            ];
            $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
            if ($response['httpCode'] >= 300) {
                $mensagem_retorno   .= '<div class="text-danger h4">Quantidade não atualizada no Mercado Livre</div>';
                //echo "<pre>"; print_r($response); echo "</pre>";
            } else {
                $mensagem_retorno   .= '<div class="text-success h4">Quantidade atualizada no Mercado Livre (' . $quantidade . ' unidades)</div>';
                $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
            }
        }

        //Atualização Preço
        /*$body = ["price" => round($preco, 2)];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Preço não atualizado no Mercado Livre</div>';
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Preço  atualizado no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }*/

        //Atualização de titulo

        $video_complemento  = explode("=", $this->video);
        $video_id = (isset($video_complemento[1]) ? $video_complemento[1] : "");
        $nome = $this->nome;
        $titulo = mb_convert_encoding($nome, 'UTF-8', 'UTF-8');
        $body = [
            //"title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
            "title" => mb_substr($titulo, 0, 60),
            "video_id" => $video_id,
            'attributes' => [
                [
                    'id' => 'PART_NUMBER',
                    'name' => 'Número da peça',
                    'value_id' => NULL,
                    'value_name' => $this->codigo_global,
                    'value_struct' => NULL,
                    'attribute_group_id' => 'DFLT',
                    'attribute_group_name' => 'Outros',
                ],
                [
                    "id" => "EAN",
                    "value_name" => $this->codigo_barras
                ],
            ]

        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Título atualizado no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }

        //Atualização das imagens
        $body = [
            "pictures" => $this->getUrlImagesML(),
        ];
        //echo "<pre>"; print_r($body); echo "</pre>";
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        //echo "<pre>"; print_r($response); echo "</pre>";
        //die;
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Imagens  atualizadas no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }

        //Atualização da Descrição
        $page = $this->nome . "\n\nAPLICAÇÃO:\n\n" . $this->aplicacao . $this->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo é o do Motorista.\n\n* Lado Direito é o do Passageiro.";
        //$page = str_replace("\n", "", $page);
        $page = str_replace("'", "", $page);
        $page = str_replace("<p>", " ", $page);
        $page = str_replace("</p>", " ", $page);
        $page = str_replace("<br>", "\n", $page);
        $page = str_replace("<BR>", "\n", $page);
        $page = str_replace("<br/>", "\n", $page);
        $page = str_replace("<BR/>", "\n", $page);
        $page = str_replace("<strong>", " ", $page);
        $page = str_replace("</strong>", " ", $page);
        $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
        $page = str_replace('</span>', " ", $page);
        $page = str_replace('<span>', " ", $page);
        $page = str_replace('<ul>', " ", $page);
        $page = str_replace('</ul>', " ", $page);
        $page = str_replace('<li>', "\n", $page);
        $page = str_replace('</li>', " ", $page);
        $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
        $page = str_replace('<h1>', " ", $page);
        $page = str_replace('</h1>', " ", $page);
        $page = str_replace('<h2>', " ", $page);
        $page = str_replace('</h2>', " ", $page);
        $page = str_replace('<h3>', " ", $page);
        $gege = str_replace('</h3>', " ", $page);
        $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
        $page = str_replace('>>>', "(", $page);
        $page = str_replace('<<<', ")", $page);
        $page = str_replace('<u>', " ", $page);
        $page = str_replace('</u>', "\n", $page);
        $page = str_replace('<b>', " ", $page);
        $page = str_replace('</b>', " ", $page);
        $page = str_replace('<o:p>', " ", $page);
        $page = str_replace('</o:p>', " ", $page);
        $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
        $page = str_replace('<del>', " ", $page);
        $page = str_replace('</del>', " ", $page);
        $page = str_replace('/', "-", $page);
        $page = str_replace('<em>', " ", $page);
        $page = str_replace('<-em>', " ", $page);
        //echo "<pre>"; print_r(substr($page, 70, 5)); echo "</pre>";

        $body = ['plain_text' => $page];
        //echo "<pre>"; print_r($body); echo "</pre>";
        $response = $meli->put("items/{$meli_id}/description?api_version=2&access_token=" . $token, $body, []);
        //echo "<pre>"; print_r($response); echo "</pre>";
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Descrição não atualizada no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Descrição atualizada no Mercado Livre</div>';
        }

        //Atualização da Categoria
        $body = [
            "category_id" => utf8_encode($this->subcategoria->meli_id),
            /*'attributes' =>[
                    [
                        "id"=> "MODEL",
                        "name"=> "Modelo",
                        "value_id"=> null,
                        "value_name"=> "Bebedouro",
                        "value_struct"=> null,
                        "attribute_group_id"=> "OTHERS",
                        "attribute_group_name"=> "Outros"
                    ],
                ]*/
        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Categoria não atualizada no Mercado Livre</div>';
            //echo "<pre>"; print_r($response); echo "</pre>";
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Categoria atualizada no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }

        //Atualização da Marca
        $marca_produto = MarcaProduto::find()->andWhere(['=', 'id', $this->marca_produto_id])->one();
        if ($marca_produto) {
            $body = [
                'attributes' => [
                    [
                        "id" => "BRAND",
                        "name" => "Marca",
                        "value_id" => null,
                        "value_name" => $marca_produto->nome,
                        "value_struct" => null,
                        "attribute_group_id" => "OTHERS",
                        "attribute_group_name" => "Outros"
                    ],
                ]
            ];
            $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
            if ($response['httpCode'] >= 300) {
                $mensagem_retorno   .= '<div class="text-danger h4">Marca não atualizada no Mercado Livre</div>';
            } else {
                $mensagem_retorno   .= '<div class="text-success h4">Marca atualizada no Mercado Livre</div>';
                $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
            }
        }

        //Atualizar codigo peca
        $body = [
            'attributes' => [
                [
                    "id" => "PART_NUMBER",
                    "name" => "Número de peça",
                    "value_id" => null,
                    "value_name" => $this->codigo_global,
                    "value_struct" => null,
                    "attribute_group_id" => "OTHERS",
                    "attribute_group_name" => "Outros"
                ],
            ]
        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
        //echo "<pre>"; print_r($response); echo "</pre>";
        if ($response['httpCode'] >= 300) {
            $mensagem_retorno   .= '<div class="text-danger h4">Numero da Peca n  o atualizada no Mercado Livre</div>';
        } else {
            $mensagem_retorno   .= '<div class="text-success h4">Numero Peca atualizada no Mercado Livre</div>';
            $link       = '<div class="h4"><a class="text-primary" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">LINK (' . $meli_origem . ')</a></div>';
        }


        $mensagem_retorno .= $link;
        return $mensagem_retorno;
    }


    public function atualizarMLVideo()
    {
        $retorno = array();

        $produtos_filiais = ProdutoFilial::find()->andWhere(['is not', "meli_id", null])->andWhere(['=', 'produto_id', $this->id])->orderBy(["filial_id" => SORT_ASC])->all();

        $video_id = "";
        if ($this->video == null) {
            $video_id = "rqvlr169tfE";
        } else {
            $video_complemento  = explode("=", $this->video);
            if (isset($video_complemento[1])) {
                $video_codigo       = explode("&", $video_complemento[1]);
                $video_id           = $video_codigo[0];
            } else {
                $retorno[$this->id]["meli_id_status"] = "Video não encontrado";
                return $retorno;
            }
        }

        //Update Imagem
        $body = [
            "video_id" => $video_id,
        ];

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');

        foreach ($produtos_filiais as $j => $produto_filial) {

            $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
            echo "\n" . $j . " - " . $produto_filial->id . " - " . $produto_filial->filial_id;

            if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
                echo " - Filial fora do ML";
                continue;
            }

            $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');

            $meliAccessToken    = $response->access_token;

            if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
                $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
                $response = $meli->put("items/{$produto_filial->meli_id}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Video não alterado";
                    echo "\nVideo não alterado (MELI_ID)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Dias de Expedição alterado";
                    echo "\nVideo (MELI_ID) - " . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
                $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Video não alterado";
                    echo "\nVideo (MELI_ID_SEM_JUROS)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Video alterado";
                    echo "\nVideo (MELI_ID_SEM_JUROS) - " . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
                $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
                $response = $meli->put("items/{$produto_filial->meli_id_full}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Video não alterado";
                    echo "\nVideo (MELI_ID_FULL)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Dias de Expedição alterado";
                    echo "\nVideo (MELI_ID_FULL)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
                $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
                $response = $meli->put("items/{$produto_filial->meli_id_flex}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Video não alterado";
                    echo "\nVideo não alterado (MELI_ID_FLEX)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Dias de Expedição alterado";
                    echo "\nVideo alterado (MELI_ID_FLEX) - " . $response["body"]->permalink;
                }
            }
        }

        return $retorno;
    }


    public function atualizarMLDescricao()
    {

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $this->id])->orderBy(["filial_id" => SORT_ASC])->all();

        $page = $this->nome . "\n\nAPLICAÇÃO:\n\n" . $this->aplicacao . $this->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo é o do Motorista.\n\n* Lado Direito é o do Passageiro.";
        $page = str_replace("'", "", $page);
        $page = str_replace("<p>", " ", $page);
        $page = str_replace("</p>", " ", $page);
        $page = str_replace("<br>", "\n", $page);
        $page = str_replace("<BR>", "\n", $page);
        $page = str_replace("<br/>", "\n", $page);
        $page = str_replace("<BR/>", "\n", $page);
        $page = str_replace("<strong>", " ", $page);
        $page = str_replace("</strong>", " ", $page);
        $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
        $page = str_replace('</span>', " ", $page);
        $page = str_replace('<span>', " ", $page);
        $page = str_replace('<ul>', " ", $page);
        $page = str_replace('</ul>', " ", $page);
        $page = str_replace('<li>', "\n", $page);
        $page = str_replace('</li>', " ", $page);
        $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
        $page = str_replace('<h1>', " ", $page);
        $page = str_replace('</h1>', " ", $page);
        $page = str_replace('<h2>', " ", $page);
        $page = str_replace('</h2>', " ", $page);
        $page = str_replace('<h3>', " ", $page);
        $gege = str_replace('</h3>', " ", $page);
        $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
        $page = str_replace('>>>', "(", $page);
        $page = str_replace('<<<', ")", $page);
        $page = str_replace('<u>', " ", $page);
        $page = str_replace('</u>', "\n", $page);
        $page = str_replace('<b>', " ", $page);
        $page = str_replace('</b>', " ", $page);
        $page = str_replace('<o:p>', " ", $page);
        $page = str_replace('</o:p>', " ", $page);
        $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
        $page = str_replace('<del>', " ", $page);
        $page = str_replace('</del>', " ", $page);
        $page = str_replace('/', "-", $page);
        $page = str_replace('<em>', " ", $page);
        $page = str_replace('<-em>', " ", $page);

        $page = str_replace("IVECO", "", str_replace("Iveco", "", $page));

        $body = ['plain_text' => $page];

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');

        $retorno = array();

        foreach ($produtos_filiais as $j => $produto_filial) {

            $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
            echo "\n" . $j . " - " . $produto_filial->id . " - " . $produto_filial->filial_id;

            if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
                echo " - Filial fora do ML";
                continue;
            }

            $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');
            $meliAccessToken    = $response->access_token;

            if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
                $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
                $response = $meli->put("items/{$produto_filial->meli_id}/description?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Nome não alterado";
                    echo "\nDescricao não alterado (MELI_ID)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Nome alterado";
                    echo "\nDescricao alterado (MELI_ID) - " . $this->nome;
                }
            }
            if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
                $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}/description?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Nome não alterado";
                    echo "\nDescricao não alterado (MELI_ID_SEM_JUROS)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Nome alterado";
                    echo "\nDescricao alterado (MELI_ID_SEM_JUROS) - " . $this->nome;
                }
            }
            if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
                $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
                $response = $meli->put("items/{$produto_filial->meli_id_full}/description?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Nome não alterado";
                    echo "\nDescricao não alterado (MELI_ID_FULL)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Nome alterado";
                    echo "\nDescricao alterado (MELI_ID_FULL)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
                $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
                $response = $meli->put("items/{$produto_filial->meli_id_flex}/description?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Nome não alterado";
                    echo "\nDescricao não alterado (MELI_ID_FLEX)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Nome alterado";
                    echo "\nDescricao alterado (MELI_ID_FLEX) - " . $this->nome;
                }
            }
        }

        return $retorno;
    }

    public function atualizarMLNome()
    {

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $this->id])->orderBy(["filial_id" => SORT_ASC])->all();

        $title = Yii::t('app', '{nome}', ['nome' => $this->nome]);
        $title = str_replace("IVECO", "", str_replace("Iveco", "", $title));
        $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');

        $retorno = array();

        foreach ($produtos_filiais as $j => $produto_filial) {

            $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
            echo "\n" . $j . " - " . $produto_filial->id . " - " . $produto_filial->filial_id;

            if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
                echo " - Filial fora do ML";
                continue;
            }

            $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');
            $meliAccessToken    = $response->access_token;

            if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
                $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
                $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Nome não alterado";
                    echo "\nNome não alterado (MELI_ID)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Nome alterado";
                    echo "\nNome alterado (MELI_ID)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
                $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Nome não alterado";
                    echo "\nNome não alterado (MELI_ID_SEM_JUROS)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Nome alterado";
                    echo "\nNome alterado (MELI_ID_SEM_JUROS)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
                $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Nome não alterado";
                    echo "\nNome não alterado (MELI_ID_FULL)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Nome alterado";
                    echo "\nNome alterado (MELI_ID_FULL)" . $response["body"]->permalink;
                }
            }
            if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
                $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
                $response = $meli->put("items/{$produto_filial->meli_id_flex}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Nome não alterado";
                    echo "\nNome não alterado (MELI_ID_FLEX)";
                } else {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Nome alterado";
                    echo "\nNome alterado (MELI_ID_FLEX)" . $response["body"]->permalink;
                }
            }
        }

        return $retorno;
    }

    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);

        $atributos              = json_encode($this->attributes);

        Log::registrarLog($atributos, "produto", $this->id, 1, ($insert) ? 1 : 2);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $atributos              = json_encode($this->attributes);

        Log::registrarLog($atributos, "produto", $this->id, 1, 3);
    }
}


/**
 * Classe para contenção de escopos da Produto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 03/12/2014
 */
class ProdutoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 03/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto.nome' => $sort_type]);
    }

    /**
     * Escopo bem específico para a página de visualização do produto, que reúne as diversas
     * relações que compoem esta renderização para facilitar a implementação/manutenção e
     * aumentar o desempenho, agrupando todos os acessos ao banco em uma só execução
     * @author Vinicius Schettino 03/12/2014
     */
    public function readyToView()
    {
        return $this->with(
            [
                'subcategoria',
                'subcategoria.categoria',
                'fabricante',
                'anosModelo',
                'atributosProduto',
                'atributosProduto.opcoes',
                'atributosProduto.opcoes.atributo',
                'atributosProduto.atributo',
                'anosModelo.modelo',
                'anosModelo.modelo.marca',
                'anosModelo.modelo.categoria',
                'imagens'
            ]
        );
    }

    public function byCodigoGlobal($codigo_global)
    {
        return $this->andWhere(['produto.codigo_global' => $codigo_global]);
    }

    public function byCodigoFabricante($codigo_fabricante)
    {
        return $this->andWhere(['produto.codigo_fabricante' => $codigo_fabricante]);
    }

    public function byCodigoFornecedor($codigo_fornecedor)
    {
        return $this->andWhere(['produto.codigo_fornecedor' => $codigo_fornecedor]);
    }

    /**
     * @author Igor Mageste 15/10/2015
     * @param $fabricante_id
     * @return $this
     */
    public function byFabricante($fabricante_id)
    {
        if (is_null($fabricante_id)) {
            return $this;
        }

        return $this->joinWith(['fabricante'])->andWhere(['fabricante.id' => $fabricante_id]);
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

        return $this->joinWith(['subcategoria'])->andWhere(['subcategoria.id' => $subcategoria_id]);
    }

    public function lojistaAtivo()
    {
        return $this->joinWith(['filial.lojista'])->andWhere(['lojista.ativo' => true]);
    }
}