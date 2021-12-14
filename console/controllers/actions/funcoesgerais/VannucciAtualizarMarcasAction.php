<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\MarcaProduto;
use common\models\ProdutoFilial;

class VannucciAtualizarMarcasAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de Marcas: \n\n";

        $marcas = array();
        $marcas = [
            "9959"	=> "3M",
            "91"	=> "AGRALE",
            "1279"	=> "AIRTECH",
            "2550"	=> "ARLA",
            "372"	=> "ATF",
            "140"	=> "AUTIMPEX",
            "2220"	=> "AXLETECH",
            "644"	=> "BINS",
            "729"	=> "BORGWARNER",
            "726"	=> "BORGWARNER-REMANU",
            "587"	=> "BOSH",
            "2202"	=> "BRUMKO",
            "49"	=> "CABOVEL",
            "501"	=> "CERCENA",
            "600"	=> "CINDUMEL",
            "486"	=> "CINPAL",
            "2010"	=> "CONTITECH",
            "579"	=> "CUMMINS",
            "40"	=> "DELAROSA",
            "2017"	=> "DML",
            "9945"	=> "DURAMETAL",
            "850"	=> "DUROLINE",
            "500"	=> "EATON",
            "2144"	=> "EATON-EMBREAGEM",
            "126"	=> "EATON-REMANU",
            "2018"	=> "ECKISIL",
            "2069"	=> "ELRING",
            "2209"	=> "EURORICAMBI",
            "1154"	=> "FACHINI",
            "1000"	=> "FIRESTONE",
            "1196"	=> "FLEETGUARD",
            "9956"	=> "FLEXYON",
            "1050"	=> "FORD",
            "421"	=> "FRAM",
            "475"	=> "FRUM",
            "440"	=> "FUNDIÇÃOBATISTA",
            "1227"	=> "FUNDIMIG",
            "2563"	=> "HELLA",
            "847"	=> "INPEL",
            "9937"	=> "IPIRANGA",
            "494"	=> "IVECO",
            "1035"	=> "JARFEX",
            "1245"	=> "JOST",
            "731"	=> "KL",
            "757"	=> "KNORR",
            "695"	=> "KOSTAL",
            "1238"	=> "KS",
            "2000"	=> "LOTE",
            "511"	=> "MASTER",
            "660"	=> "MAXION",
            "98"	=> "MBB",
            "199"	=> "MECPAR",
            "502"	=> "MERITOR",
            "367"	=> "METAGAL",
            "133"	=> "MODEFER",
            "1272"	=> "MWM",
            "285"	=> "NAKATA",
            "2523"	=> "NAKATA-BARRAS",
            "2522"	=> "NAKATA-SPICER",
            "9955"	=> "OLIVO",
            "503"	=> "PARAFLU",
            "275"	=> "PARKERRACOR(FILTROS)",
            "881"	=> "PRESTOLITEIMP",
            "248"	=> "REI",
            "800"	=> "REIPARTS",
            "256"	=> "REMANUFATURADO",
            "396"	=> "RIOSULENSE",
            "238"	=> "SACHS",
            "455"	=> "SCHADEK",
            "869"	=> "SIFCO",
            "754"	=> "SKF",
            "460"	=> "SOROCARD",
            "338"	=> "SPALL",
            "687"	=> "SPICER",
            "1305"	=> "SISIN",
            "914"	=> "SUSPENSYS",
            "828"	=> "SUSPENTECH",
            "482"	=> "THERMOID",
            "423"	=> "TIMKEN",
            "159"	=> "TIPH-MAXGEAR",
            "1253"	=> "TRW",
            "215"	=> "UNIPAC",
            "2200"	=> "VALVOLINE",
            "904"	=> "VARGA",
            "110"	=> "VW",
            "553"	=> "WABCO",
            "265"	=> "WAHLER",
            "1271"	=> "ZF",
            "9958"	=> "ZAPPAROLI",
            "2078"  => "RODOFIBRA",
            "2039"  => "AS FIBRAS",
            "2092"  => "BONFANTI",
            "3546"  => "FST",
            "918"   => "DONALDSON",
            "2035"  => "CRM",
            "45"    => "QUALYTA",
            "270"   => "GUABOR",
            "282"   => "AS FIBRAS",
            "2221"  => "TAS ITALIA",
            "493"   => "LS",
            "444"   => "MAHLE",
            "839"    => "BORRACHAFLEX",
            "1014"    => "TECPEL",
            "599"    => "METAX",
            "381"    => "FORT PECAS",
            "870"    => "COFAP",
            "316" => "VISION",
            "9952" => "BIRK",
            "229" => "UNIVERSAL",
            "2094" => "REDE",
            "751"     => "ONIX",
            "2197"   => "ACOCUBO",
            "991" => "ZAPPAROLI",
            "2289"   =>  "GLOBO",
            "2171"   => "WP IMPORTADOS",
            "845"   => "VERLI",
            "582"  => "ROMANAPLAST",
            "64"  => "TVL",
            "708" => "3R RUBBER",
            "194"  => "KM BRASIL",
            "287" => "MASTRA",
            "543" =>   "BRASLUZ",
            "429" => "AMALCABURIO",
            "2087" => "FABBOF",
            "153" => "ZINI GUELL",
            "170" => "PRADOLUX",



        ];
        
        $produto_filiais = ProdutoFilial::find()->andWhere(['=','filial_id',38])->all();
        
        foreach ($produto_filiais as $i => &$produto_filial){
            
            echo "\n".$i." - ".$produto_filial->id." - ".$produto_filial->produto->codigo_fabricante;
            
            $codigo_fabricante  = explode("-",$produto_filial->produto->codigo_fabricante);
            
            if (array_key_exists(1,$codigo_fabricante)){
                echo $codigo_fabricante[1];
                
                $sufixo             = $codigo_fabricante[1];
                $sufixo             = str_replace("*1","",$sufixo);
                $sufixo             = str_replace("*2","",$sufixo);
                $sufixo             = str_replace("*3","",$sufixo);
                $sufixo             = str_replace("*4","",$sufixo);
                $sufixo             = str_replace("*5","",$sufixo);
                $sufixo             = str_replace("*6","",$sufixo);
                $sufixo             = str_replace("*7","",$sufixo);
                $sufixo             = str_replace("*8","",$sufixo);
                $sufixo             = str_replace("*9","",$sufixo);
                
                if (array_key_exists($sufixo,$marcas)){
                    
                    $marca_produto = MarcaProduto::find()->andWhere(['=','nome',$marcas[$sufixo]])->one();
                    
                    if($marca_produto){
                        $produto = Produto::find()->andWhere(['=','id',$produto_filial->produto_id])->one();

			            if($sufixo == "726" || $sufixo == "126" || $sufixo == "2000" || $sufixo == "256"){
                            $produto->e_usado = true;
                        }

                        $produto->marca_produto_id = $marca_produto->id;
                        if($produto->save()){
                            echo " - Marca alterada";
                        }
                        else{
                            echo " - Marca Não alterada";
                        }
                    }
                    else{
                        echo "Marca não encontrada no PeçaAgora";
                    }
                }
                else{
                    echo "Marca fora da planilha de marcas";
                }
            }
        }
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
