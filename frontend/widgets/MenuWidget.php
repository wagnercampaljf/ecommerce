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
                                  <div class="col-md-4">
                                     <ul class="list-links">
                                        <li><h3 class="list-links-title">'.$categoria->nome.'</h3></li>';

            $subcategorias = Subcategoria::find()->andWhere(["=","categoria_id",$categoria->id])->all();
            foreach ($subcategorias as $k => $subcategoria){
                $menuA .=                   '<li><a href="/search?nome='.$subcategoria->nome.'">'.$subcategoria->nome.'</a></li>';
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

        $categorias_selecionadas = [11, 10, 6, 2, 9, 6, 1, 12, 7, 15];
        $categorias = Categoria::find()->andWhere(['id' => $categorias_selecionadas])->orderBy('nome')->all();

        $menuHorizontal = '';
        foreach ($categorias as $k => $categoria){
            $menuHorizontal .=  '<li class="dropdown" ><a href="/search?nome='.$categoria->nome.'" class="text-menu">'.$categoria->nome.'</a>
                                 <div class="dropdown-content">';

            $subcategorias = Subcategoria::find()->andWhere(['=','categoria_id',$categoria->id])->limit(20)->all();
            foreach($subcategorias as $x => $subcategoria){
                $menuHorizontal .= '                       
                            <div class="row" >
                                <div class="col-md-5">
                                    <ul class="list-links">
                                        <li> <h3 class="list-links-title"><a href="/search?nome='.$subcategoria->nome.'">'.$subcategoria->nome.'</a></h3> </li>';

                 /*$menuProdutos = '';
                 $produtos = Produto::find()->andWhere(['=','subcategoria_id',$subcategoria->id])->limit(3)->all();
                 foreach ($produtos as $y => $produto){
                     $menuProdutos .= '<li><a href="#">'.$produto->nome.'</a></li>';
                 }*/

                 $menuHorizontal .=      //"$menuProdutos
                                    "</ul>
                                </div>
                            </div>";
            }

            $menuHorizontal .=  '    </div>
                                 </li>';
        }

        $retorno .= '
        <div class="container">
            <div id="responsive-nav">
                <ul class="menu-list" >
                    '.$menuHorizontal.'
                </ul>
            </div>
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





