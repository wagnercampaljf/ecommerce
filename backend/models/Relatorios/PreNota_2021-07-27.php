<?php

namespace backend\models\Relatorios;

use common\models\Padraopdf;
use Yii;
use console\controllers\actions\omie\Omie;
use backend\models\PedidoMercadoLivreSearch;
use yii\widgets\ListView;
use common\models\Administrador;
use common\models\Imagens;
use common\models\PedidoMercadoLivreShipments;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\Produto;
use common\models\ProdutoFilial;

class PreNota extends Padraopdf
{
    private $pdf;
    public function init($pedido_mercado_livre)
    {
        //echo "<pre>"; print_r($pedido_mercado_livre); echo "</pre>"; die;
        $this->pdf = $this->setInit('L', 'Pré-Nota');
        $this->pdf->SetAutoPageBreak(true, 20);
        $this->pdf->AddPage();
        $this->pdf->Ln(1);
        $this->setRelatorio($pedido_mercado_livre);
        $this->pdf->Output('pre_nota_'.$pedido_mercado_livre->pedido_meli_id.'.pdf', 'I');
    }

    public function setRelatorio($pedido_mercado_livre)
    {
        
        //echo $html;
        //$this->pdf->writeHTML($html, true, false, true, false, '');
        
        $produtos = $this->getSql($pedido_mercado_livre->id);
        
        $administrador = Administrador::find()->andWhere(["=", "id", $pedido_mercado_livre->autorizado_por])->one();

        $count = count($produtos);

        $this->pdf->SetFont('helvetica', 'B', 20);
        $conta = "";
        switch($pedido_mercado_livre->user_id){
            case "193724256":
                $conta = "Mercado Livre Principal";
                break;
            case "435343067":
                $conta = "Mercado Livre Filial";
                break;
        }

        $tipo_envio = ($pedido_mercado_livre->shipping_id != "") ? "Mercado Envios" : "Terceiros";
        
        $data_array = explode(".", $pedido_mercado_livre->data_hora_autorizacao);
        
        $this->pdf->MultiCell(277, 20, "Nº: " . $pedido_mercado_livre->pedido_meli_id, '', 'L', false, 1);
        $this->pdf->SetFont('helvetica', 'N', 14);
        $this->pdf->MultiCell(130, 10, "Conta: " . $conta, '', 'L', false, 0);        
        $this->pdf->MultiCell(130, 10, "Envio: " . $tipo_envio, '', 'L', false, 1);
        $this->pdf->MultiCell(130, 10, "Nome: " . $pedido_mercado_livre->buyer_first_name." ".$pedido_mercado_livre->buyer_last_name, '', 'L', false, 0);
        $this->pdf->MultiCell(130, 10, str_replace(" ", "", $pedido_mercado_livre->buyer_doc_type).": " . $pedido_mercado_livre->buyer_doc_number, '', 'L', false, 1);
        $this->pdf->MultiCell(130, 10, "Data/Hora Autorização: " . $data_array[0], '', 'L', false, 0);
        $this->pdf->MultiCell(130, 10, "Autorizado por: " . $administrador->nome, '', 'L', false, 1);
        //$this->pdf->MultiCell(277, 4, "$count Tipos de Produto em Estoque", '', 'L', false, 1);
        $this->pdf->ln(4);

        $this->setCabecalho();
        
        $i = 0;

        foreach ($produtos as $produto) {
            $this->pdf->SetFont('helvetica', 'N', 12);

            //$h = $this->pdf->getStringheight(92, $produto['nome']) + 1.8;
            $h = 36;            
            $codigo_pa = "PA" . $produto['id'];
            
            //$this->pdf->MultiCell(44, $h, $this->pdf->Image('https://www.pecaagora.com/imagens/produto_230615/230615_1.webp', 0, 0, 50, 50, 'WEBP', 'http://www.pecaagora.com/p/'.$produto['id'], '', false, 150, '', false, false, 1, false, false, false), 0, 'L', false, 0);
            //$this->pdf->writeHTMLCell(100, 50, 10, 10, $this->pdf->Image('https://www.pecaagora.com/imagens/produto_230615/230615_1.webp', 0, 0, 50, 50, 'WEBP', 'http://www.pecaagora.com/p/'.$produto['id'], '', false, 150, '', false, false, 1, false, false, false));

            $link_produto = 'http://www.pecaagora.com/p/'.$produto['id'];
            
            //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
            $imagem_caminho = "";
            $imagem = Imagens::find()->andWhere(["=", "produto_id", $produto['id']])->orderBy(["ordem" => SORT_ASC])->one();
            if($imagem){
                $imagem_caminho = "https://www.pecaagora.com/imagens/produto_".$produto["id"]."/".$produto["id"]."_".$imagem->ordem.".webp";
                copy($imagem_caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/' . $imagem->id . ".webp");
                $resultado      = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg ' . $imagem->id . '.webp');
                $caminho        = 'https://www.pecaagora.com/frontend/web/assets/img/imagens_temporarias/' . $imagem->id . '.jpg';
            }
            //$caminho_imagem = "https://www.pecaagora.com/imagens/produto_230615/230615_1.webp";
            //$imagem = $this->pdf->Image($caminho_imagem, $this->pdf->getX(), $this->pdf->getY(), 44, 44, 'WEBP', $link_produto, '', true, 300, 'J', false, false, 1, false, false, false);
            
            $imagem = $this->pdf->Image($caminho, $this->pdf->getX(), $this->pdf->getY(), 44, 44, 'JPG', $link_produto, '', true, 300, 'J', false, false, 1, false, false, false);
            
            //print_r($imagem); die;
            
            $this->pdf->MultiCell(47, $h, $imagem, 0, 'L', false, 0);
            $this->pdf->MultiCell(27, $h, $codigo_pa, 0, 'L', false, 0);
            $this->pdf->MultiCell(90, $h, $produto['nome'], 0, 'L', false, 0);
            $this->pdf->MultiCell(35, $h, $produto['codigo_global'], 0, 'L', false, 0);
            $this->pdf->MultiCell(30, $h, $produto['codigo_fabricante'], 0, 'L', false, 0);
            $this->pdf->MultiCell(20, $h, $produto['quantidade'], 0, 'R', false, 0);
            $this->pdf->MultiCell(25, $h, number_format($produto['valor'], 2, ',', ' '), 0, 'R', false, 1);
            $i++;

            if ($this->pdf->getY() > 180) {
                $this->pdf->AddPage();
                $this->setCabecalho();
            }
        }

        $xc = 100;
        $yc = 100;
        $style = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $this->pdf->ln(10);
        $this->pdf->Line(10, $this->pdf->getY(), 284, $this->pdf->getY());
        $this->pdf->ln(3);
        $this->pdf->MultiCell(30, 40, "Observação: " , '', 'L', false, 0);
        $this->pdf->MultiCell(247, 40, $pedido_mercado_livre->observacao, '', 'L', false, 1);

    }

    public function setCabecalho()
    {
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->MultiCell(47, 4, 'Imagem', 'B', 'C', false, 0);
        $this->pdf->MultiCell(27, 4, 'PA', 'B', 'L', false, 0);
        $this->pdf->MultiCell(90, 4, 'Nome', 'B', 'L', false, 0);
        $this->pdf->MultiCell(35, 4, 'Cod. Global', 'B', 'L', false, 0);
        $this->pdf->MultiCell(30, 4, 'Cod. Fabr.', 'B', 'L', false, 0);
        $this->pdf->MultiCell(20, 4, 'QTD.', 'B', 'R', false, 0);
        $this->pdf->MultiCell(25, 4, 'Valor Cto', 'B', 'R', false, 1);
        //$this->pdf->MultiCell(10, 4, '', 'B', 'L', false, 1);
        $this->pdf->ln(2);
    }

    public function getSql($pedido_mercado_livre_id)
    {

        $sql = "select	filial_id,
                		produto.id, 
                		produto.nome, 
                		produto.codigo_global, 
                		produto.codigo_fabricante, 
                		pedido_mercado_livre_produto_produto_filial.quantidade,
                		pedido_mercado_livre_produto_produto_filial.valor,
                		pedido_mercado_livre_produto_produto_filial.observacao
                from		pedido_mercado_livre_produto
                		left join pedido_mercado_livre_produto_produto_filial on pedido_mercado_livre_produto.id = pedido_mercado_livre_produto_produto_filial.pedido_mercado_livre_produto_id 
                		left join produto_filial on produto_filial.id = pedido_mercado_livre_produto_produto_filial.produto_filial_id 
                		left join produto on produto.id = produto_filial.produto_id 
                where	pedido_mercado_livre_id = ".$pedido_mercado_livre_id;

        return $query = Yii::$app->db->createCommand($sql)->queryAll();
    }
}
