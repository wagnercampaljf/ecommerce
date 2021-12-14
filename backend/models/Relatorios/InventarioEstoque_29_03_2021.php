<?php

namespace backend\models\Relatorios;

use common\models\Padraopdf;
use Yii;

class InventarioEstoque extends Padraopdf
{
    private $pdf;
    public function init($filial)
    {
        $this->pdf = $this->setInit('L', 'Invertário Estoque');
        $this->pdf->SetAutoPageBreak(true, 20);
        $this->pdf->AddPage();
        $this->pdf->Ln(1);
        $this->setRelatorio($filial);
        $this->pdf->Output('InventarioEstoque.pdf', 'I');
    }

    public function setRelatorio($filial)
    {
        $query = $this->getSql($filial);
        $count = count($query);
        $this->pdf->SetFont('helvetica', 'N', 10);
        $this->pdf->MultiCell(277, 4, "Filial: " . $filial = 96 ? 'São Paulo' : 'Minas Gerais', '', 'L', false, 1);
        $this->pdf->MultiCell(277, 4, "$count Tipos de Produto em Estoque", '', 'L', false, 1);
        $this->pdf->ln(4);

        $this->setCabecalho();

        $i = 0;
        $total_venda = 0;
        $total_quantidade = 0;

        foreach ($query as $produto) {
            $this->pdf->SetFont('helvetica', 'N', 7);
            $total_venda +=  $produto['menor_valor'];
            $total_quantidade +=  $produto['quantidade'];
            $h = $this->pdf->getStringheight(92, $produto['nome']) + 1.8;

            $this->pdf->MultiCell(20, $h, $produto['id'], 0, 'L', false, 0);
            $this->pdf->MultiCell(92, $h, $produto['nome'], 0, 'L', false, 0);
            $this->pdf->MultiCell(25, $h, $produto['codigo_global'], 0, 'L', false, 0);
            $this->pdf->MultiCell(30, $h, $produto['codigo_fabricante'], 0, 'L', false, 0);
            $this->pdf->MultiCell(12, $h, $produto['quantidade'], 0, 'L', false, 0);
            $this->pdf->MultiCell(30, $h, number_format($produto['menor_valor'], 2, ',', ' '), 0, 'R', false, 0);
            $this->pdf->MultiCell(30, $h, $produto['localizacao'], 0, 'L', false, 0);
            $this->pdf->MultiCell(30, $h, $produto['marca'], 0, 'L', false, 0);
            $this->pdf->MultiCell(10, $h, '', 'B', 'L', false, 1);
            $i++;

            if ($this->pdf->getY() > 180) {
                $this->pdf->AddPage();
                $this->setCabecalho();
            }
        }

        $xc = 100;
        $yc = 100;
        $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $this->pdf->ln(8);
        $this->pdf->Line(10, $this->pdf->getY(), 290, $this->pdf->getY());


        $this->pdf->ln(8);
        $this->pdf->SetFont('helvetica', 'N', 10);
        $this->pdf->MultiCell(277, 4, "Quantidade Total em estoque (Produtos): $total_quantidade", '', 'R', false, 1);
        $this->pdf->MultiCell(277, 4, 'Valor Total em estoque (Preço Venda):  R$' . number_format($total_venda, 2, ',', ''), '', 'R', false, 1);
        $this->pdf->MultiCell(277, 4, 'Valor Total em estoque (Preço Compra):  R$0,00', '', 'R', false, 1);
    }

    public function setCabecalho()
    {
        $this->pdf->SetFont('helvetica', 'N', 10);
        $this->pdf->MultiCell(20, 4, 'PA', 'B', 'L', false, 0);
        $this->pdf->MultiCell(92, 4, 'Nome', 'B', 'L', false, 0);
        $this->pdf->MultiCell(25, 4, 'Cod. Global', 'B', 'L', false, 0);
        $this->pdf->MultiCell(30, 4, 'Cod. Fabr.', 'B', 'L', false, 0);
        $this->pdf->MultiCell(12, 4, 'QTD.', 'B', 'L', false, 0);
        $this->pdf->MultiCell(30, 4, 'Valor Venda', 'B', 'R', false, 0);
        $this->pdf->MultiCell(30, 4, 'Localizaçao', 'B', 'L', false, 0);
        $this->pdf->MultiCell(30, 4, 'Marca', 'B', 'L', false, 0);
        $this->pdf->MultiCell(10, 4, '', 'B', 'L', false, 1);
        $this->pdf->ln(2);
    }

    public function getSql($filial)
    {

        $sql = "select p.id, p.nome, p.codigo_global, p.codigo_fabricante, pf.quantidade, vpmm.menor_valor, p.localizacao,
        mp.nome as marca
        from produto p
        join produto_filial pf on pf.produto_id = p.id
        left join marca_produto mp on mp.id = p.marca_produto_id
        left join valor_produto_menor_maior vpmm on vpmm.produto_id = p.id
        where pf.filial_id = $filial and pf.quantidade > 0
        order by p.id";

        return $query = Yii::$app->db->createCommand($sql)->queryAll();
    }
}

