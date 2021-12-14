<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\Imagens;
use yii\base\Action;
use common\models\ProdutoFilial;
use Yii;


class AmazonGerarPlanilhaDadosCompletaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de geração da planilha com os dados para Amazon: \n\n";

        //Gera planilha dos produtos já presentes na Amazon
        $arquivo = fopen("/var/tmp/produtos_amazon_28-02-2021.csv", "a");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/log_produtos_amazon_tab.csv", 'r'); //Abre arquivo com skus(produto_filial_id) que já estão cadastrados na Amazon, para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ";

            if($i == 0){
                // fwrite($arquivo, '"feed_product_type";"item_sku";"brand_name";"recommended_browse_nodes";"item_name";"manufacturer";"part_number";"external_product_id";"external_product_id_type";"fit_type";"compatible_with_vehicle_type";"standard_price";"quantity";"condition_type";"main_image_url";"other_image_url1";"other_image_url2";"other_image_url3";"other_image_url4";"other_image_url5";"other_image_url6";"other_image_url7";"other_image_url8";"swatch_image_url";"parent_child";"parent_sku";"relationship_type";"variation_theme";"update_delete";"model";"model_name";"product_description";"inner_material_type";"outer_material_type";"voltage_unit_of_measure";"wattage_unit_of_measure";"shaft_style_type";"color_name";"color_map";"oe_manufacturer";"department_name";"bullet_point1";"bullet_point2";"bullet_point3";"bullet_point4";"bullet_point5";"specific_uses_keywords";"target_audience_keywords";"thesaurus_attribute_keywords";"generic_keywords";"oem_equivalent_part_number1";"oem_equivalent_part_number2";"oem_equivalent_part_number3";"oem_equivalent_part_number4";"oem_equivalent_part_number5";"catalog_number";"thesaurus_subject_keywords";"size_name";"material_type";"control_type";"special_features1";"special_features2";"special_features3";"special_features4";"special_features5";"light_source_type";"operation_mode";"lifestyle";"style_name";"voltage";"wattage";"amperage_unit_of_measure";"amperage";"platinum_keywords1";"platinum_keywords2";"platinum_keywords3";"platinum_keywords4";"platinum_keywords5";"abpa_partslink_number1";"abpa_partslink_number2";"abpa_partslink_number3";"abpa_partslink_number4";"item_dimensions_unit_of_measure";"item_diameter_unit_of_measure";"size_map";"item_length";"item_height";"item_width";"website_shipping_weight_unit_of_measure";"website_shipping_weight";"item_display_diameter_unit_of_measure";"item_diameter_derived";"item_display_diameter";"legal_disclaimer_description";"cpsia_cautionary_statement";"cpsia_cautionary_description";"import_designation";"country_of_origin";"warranty_description";"fabric_type";"mfg_warranty_description_type";"offering_end_date";"merchant_shipping_group_name";"max_order_quantity";"offering_start_date";"item_package_quantity";"condition_note";"sale_price";"sale_from_date";"sale_end_date";"product_tax_code";"product_site_launch_date";"merchant_release_date";"restock_date";"map_price";"list_price";"fulfillment_latency";"max_aggregate_ship_quantity";"offering_can_be_gift_messaged";"offering_can_be_giftwrapped";"is_discontinued_by_manufacturer"');

                fwrite($arquivo, '"sku";"price";"minimum-seller-allowed-price";"maximum-seller-allowed-price";"quantity"');


                continue;
            }

            //fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'"');

            //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $linhaArray[0]])->one();

            if($produto_filial){

                $quantidade = 0;
                if($produto_filial->filial_id == 38 || $produto_filial->filial_id == 43 || $produto_filial->filial_id == 60 || $produto_filial->filial_id == 72 || $produto_filial->filial_id == 97){
                    $quantidade = $produto_filial->quantidade;
                }

                $preco = round($produto_filial->getValor(), 2);

                echo $produto_filial->id." - ".$preco;

                $imagens = Imagens::find()->select(['ordem'])->andWhere(['=','produto_id',$produto_filial->produto_id])->orderBy('ordem')->one();
                $imagem_url = "";
                if($imagens){
                    $imagem_url = "https://www.pecaagora.com/site/get-link?produto_id=" . $produto_filial->produto_id . "&ordem=".$imagens->ordem;
                }



                echo $produto_filial->id."---".$preco."---".$produto_filial->produto->nome."---".$produto_filial->quantidade;


                // fwrite($arquivo, "\n".'"autoaccessorymisc";"'.$produto_filial->id.'";"OPT";"19701957011";"'.$produto_filial->produto->nome.'";"OPT";"PA'.$produto_filial->produto_id.'";"";"ASIN";"Universal";"Tudo Acima";"'.$preco.'";'.$produto_filial->quantidade.';"'.$produto_filial->produto->e_usado.'";"'.$imagem_url.'";"";"";"";"";"";"";"";"";"";"";"";"";"";"Atualizacao";"model";"'.$produto_filial->produto->nome.'";"";"";"";"";"";"";"";"";"";"";"Códigos Similares'.$produto_filial->produto->codigo_similar.'";"'.$produto_filial->produto->aplicacao.'";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"'.$produto_filial->produto->aplicacao.'";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"CM";"";"";"'.$produto_filial->produto->profundidade.'";"'.$produto_filial->produto->altura.'";"'.$produto_filial->produto->largura.'";"KG";"'.$produto_filial->produto->peso.'";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"1";"";"";"";"";"";"";"";"";"";"";"";"";"";"";""');
                $preco=$preco*1.15;


                $preco = number_format($preco, 2, '.', '');

                $min_preco= $preco *0.999 ;

                $max_preco = $preco * 1.1;

                $min_preco = number_format( $min_preco, 2, '.', '');

                $max_preco = number_format($max_preco, 2, '.', '');

                fwrite($arquivo, "\n".$produto_filial->id.";".$preco.";".$min_preco.";".$max_preco.";".$produto_filial->quantidade.";");


            }else{
                echo " - Produto nao encontrado ";

            }
        }

        fclose($arquivo);
        
        echo "\n\nFIM da rotina de geração da planilha com os dados para Amazon!";
    }
}