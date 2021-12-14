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

 * @property resource $imagem_sem_logo
 * @property resource $imagem_zoom
 * @property boolean $e_usado
 * @property boolean $e_medidas_conferidas
 * @property boolean $e_ativo
 * @property boolean $e_valor_bloqueado
 *
 * @property ProdutoFilial[] $produtoFilials
 * @property Fabricante $fabricante
 * @property Filial $filial
 * @property Subcategoria $subcategoria
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
            [['nome', 'codigo_global', 'subcategoria_id', 'fabricante_id'], 'required'],
            [['descricao','localizacao', 'codigo_similar', 'aplicacao_complementar', 'aplicacao', 'video', 'codigo_barras', 'cest'], 'string'],
            [['peso', 'altura', 'largura', 'profundidade', 'ipi', 'valor', 'valor_cnpj', 'valor_compra'], 'number'],
            [['fabricante_id', 'subcategoria_id', 'quantidade', 'multiplicador', 'envio', 'ordem', 'filial_id'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['codigo_global', 'codigo_montadora', 'codigo_fabricante'], 'string', 'max' => 25],
            ['codigo_global', 'unique'],
            [['slug'], 'string', 'max' => 200],
            [['micro_descricao'], 'string', 'max' => 250],
            [['anoModelo_id', 'e_usado', 'e_medidas_conferidas', 'e_ativo', 'e_valor_bloqueado'], 'safe'],
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
            'valor_compra'		=> 'Valor Compra',
            'promocao'                  => 'Promoção',
	    'imagem'                    => 'Imagem',
            'ordem'                     => 'Posição',
            'imagem_sem_logo'           => 'Imagem Sem Logo',
            'imagem_zoom'               => 'Imagem Zoom',
	    'e_usado'			=> 'É usado?',
            'e_medidas_conferidas'	=> 'Medidas Conferidas',
	    'e_ativo'			=> 'É ativo?',
         'e_valor_bloqueado'    => 'É valor bloqueado?'
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
        $imagem = Imagens::find()->andWhere(['=','produto_id',$this->id])->orderBy('ordem')->one();

        if ($imagem) {
            $src = "https://www.pecaagora.com/site/get-link?produto_id=" . $this->id . "&ordem=".$imagem->ordem;
        } else {
            $src = "https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png";
        }

        return $src;
    }

    /**
     * @author Otavio Augusto
     * @since 21/08/2017
     * @return Retorna as imagens no formato correto para o ML (Ainda Retorna o array de uma só)
     */
    public function getUrlImagesML()
    {
        $src = array();
        $imagens = $this->imagens;

	ArrayHelper::multisort($imagens,['ordem'],[SORT_ASC]);

        if (!empty($imagens)) {
            foreach ($imagens as $k => $v) {
                //$src[] = ['source' => 'https://www.pecaagora.com/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem];
		//12-02-2020$src[] = ['source' => 'http://31.220.57.2/site/get-link?produto_id=' . $this->id . '&ordem=' . $v->ordem ]; //Imagem com logo
		//$src[] = ['source' => 'http://31.220.57.2/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $v->ordem]; //Imagem sem logo

		$caminho    = "https://www.pecaagora.com/site/get-link?produto_id=".$v->produto_id."&ordem=".$v->ordem;
		copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/'.$v->id.".webp" );
		$resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg '.$v->id.'.webp');
		$src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/'.$v->id.'.jpg'];
            }
        } else {
            $src[] = ['source' => 'https://www.pecaagora.com/frontend/web/assets/img/produtos/no-image.png'];
        }
        return $src;
    }

    public function getUrlImagesMLSemLogo()
    {
        $src = array();
        $imagens = $this->imagens;

        if (!empty($imagens)) {
            foreach ($imagens as $k => $v) {
                $src[] = ['source' => 'https://www.pecaagora.com/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $v->ordem];
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
                if ($k == 0){
                    //$src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
		    $src[] = str_replace('http://','https://www.',Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem.'&b2w.jpg');
                }
                else{
                    //$src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
		    $src[] = str_replace('http://','https://www.',Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem.'&b2w.jpg');
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
		$caminho    = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=".$imagem->produto_id."&ordem=".$imagem->ordem;
                copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/'.$imagem->id."_sem_logo.webp" );
                $resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg '.$imagem->id.'_sem_logo.webp');
                $src[] = 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.'_sem_logo.jpg';
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
	    ArrayHelper::multisort($imagens,['ordem'],[SORT_ASC]);
	    $ordem = ArrayHelper::getValue($imagens, '0.ordem');
            if ($logo) {
                $src = Url::base(true) . '/site/get-link?produto_id=' . $this->id . '&ordem='.$ordem;
            } else {
                $src = Url::base(true) . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem='.$ordem;
            }
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
                    $src[] = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link?produto_id=' . $this->id . '&ordem=' . $imagem->ordem;
                }
            } else {
                foreach ($imagens as $imagem) {
                    $src[] = str_replace('http://','https://www.',Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/get-link-sem-logo?produto_id=' . $this->id . '&ordem=' . $imagem->ordem.'&b2w.jpg');
                }
            }
        } else {
            $src[] = Url::base(true) . '/frontend/web/assets/img/produtos/no-image.png';
        }

        return $src;
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
