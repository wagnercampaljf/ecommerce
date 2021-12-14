<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 26/10/2015
 * Time: 14:49
 */

namespace frontend\widgets;


use common\models\SearchModel;
use common\models\Subcategoria;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Categoria;
use common\models\Produto;

class MenuWidget extends Widget
{
    public $params = null;
    public $attributes = [];
    
    public function init()
    {

    }

    public function run()
    {

        $menuA = '';

        $categorias = Categoria::find()->orderBy('nome')->all();
        foreach($categorias as $categoria){
            $menuA .= '<li class="dropdown side-dropdown">
                            <a class="dropdown-toggle text-primary" href="#" data-toggle="dropdown" aria-expanded="true">'.$categoria->nome.'<i class="fa fa-angle-right"></i></a>
                            <div class="custom-menu">
                               <div class="row">
                                  <div class="col-md-8" style=" line-height:8px !important;">
                                     <ul class="list-links">
                                        <li ><h3 class="list-links-title">'.$categoria->nome.'</h3></li>';

            $subcategorias = Subcategoria::find()->andWhere(["=","categoria_id",$categoria->id])->all();
            foreach ($subcategorias as $k => $subcategoria){
                $menuA .=                   '<li ><a href="/search?nome='.$subcategoria->nome.'">'.$subcategoria->nome.'</a></li>';
            }
            $menuA .= '        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>';
        }

        $retorno = '<!-- menu -->
    <div id="navigation" style="background-color: #000000;">
        <div id="responsive-nav">
            <div class="category-nav show-on-click">
                <a class="category-header btn btn-outline-dark">Categorias&nbsp <i class="fa fa-bars" aria-hidden="true" style="color: white;"></i></a>
                <ul class="category-list">        
                    '.$menuA.'
                </ul>
            </div>
        </div>';

        $categorias_selecionadas = [11, 10, 6, 2, 9, 1, 16, 7, 15];
        $categorias = Categoria::find()->andWhere(['id' => $categorias_selecionadas])->orderBy('nome')->all();

        $menuHorizontal = '';
        foreach ($categorias as $k => $categoria){
            $menuHorizontal .=  '<li class="dropdown" ><a href="/search?nome='.$categoria->nome.'" class="text-menu">'.$categoria->nome.'</a>
                                 <div class="dropdown-content">';

            $subcategorias_selecionadas = [29,7,10,38,40,11,32,14,98,99,100,33,35,36,42,46,47,1,146,23,195,212,284,84,122,172];
            $subcategorias = Subcategoria::find()->andWhere(['=','categoria_id',$categoria->id])->andWhere(['id' => $subcategorias_selecionadas])->limit(4)->all();
            foreach($subcategorias as $x => $subcategoria){
                $menuHorizontal .= '
                            
                                  <div class="col-sm-6">
                                     <ul class="list-links">
                                       
                                         <li> <h3 class="list-links-title"><a href="/search?nome='.$subcategoria->nome.'">'.$subcategoria->nome.'</a></h3> </li><br>
                                    
                                      ';

                 $produtos_selecionados = [7084,7229,7230,6943,6944,7247,14623,14870,14874,10152,10153,10192,7904,7905,8317,6966,7203,10031,9010,9011,9086,9041,9219,9220,9099,9120,9126,7848,7849,7850,7844,8295,8345,15261,15665,15666,11925,24499,24501,24502,231505,240060,240061,1072,1075,1081,1342,1343,1347,1065,1066,1067,7906,7907,7908,7900,7901,7902,7750,7751,7752,14757,14759,231813,6892,6893,6929,6414,6416,6417,10664,10665,10851,11936,11937,12501,];
                 $menuProdutos = '';
                 $produtos = Produto::find()->andWhere(['=','subcategoria_id',$subcategoria->id])->andWhere(['id' =>  $produtos_selecionados])->all();
                 foreach ($produtos as $y => $produto){
                     $menuProdutos .= '<li style="font-size: 12px; text-transform: uppercase; text-overflow: ellipsis"><img style="50px; height:50px" src="'.$produto->getUrlImage().'"><a href="#">'.$produto->nome.'</a></li>';
                 }

                 $menuHorizontal .=      "$menuProdutos
                                         
                                    </ul> <hr>
                                </div> 
                               
                                  
                            ";
            }

            $menuHorizontal .=  '    </div>
                                 </li>';
        }

        $retorno .= '
            <div id="responsive-nav">
                <ul class="menu-list" >
                    <div class="container">
                      <div class="row">
                    '.$menuHorizontal.'
                      </div>
                    </div>
                </ul>
            </div>
        
        <!-- menu -->';

        /*$retorno .= '
        <div class="container">
            <div id="responsive-nav">
                <ul class="menu-list" >
                    <li class="dropdown" >
                        <a href="#" class="text-menu">Acabamentos e Cabine</a>
                        <div class="dropdown-content">
                            <div class="row" >
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li><img style="70px; height:70px" src="<?= Url::to(\'@assets/\'); ?>img/imgs-menu/volante.png">
                                        <li> <h3 class="list-links-title"><a href="#">VOLANTES</a></h3> </li><br>
                                        <li><a href="#">Volante direcão universal</a></li>
                                        <li><a href="#">Volante esportivo</a></li>
                                        <li><a href="#">Volante direção completo ...</a></li>
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                   
                                    </ul>
                                    <hr>
                                </div>
                                <div class="col-md-5">
                                    <ul class="list-links">
                                     
                                    </ul>
                                    <hr>
                                    <ul class="list-links">
                                        <li>
                          
                                    </ul>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>';*/

        $retorno .= '</div><br>
    <!-- menu horizontal-->';





        
        return $retorno;
    }
}





