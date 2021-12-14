<?php

namespace backend\models\Relatorios;

use common\models\MarcaProduto;
use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RelEstoqueVendasMarcaExcel extends Spreadsheet
{
    private $spreadsheet;
    private $sheet;
    public function init($marca_id, $dt_inicial, $dt_final)
    {
        $dt_inicial = substr($dt_inicial, -4) . '-' . substr($dt_inicial, 3, 2) . '-' . substr($dt_inicial, 0, 2);
        $dt_final = substr($dt_final, -4) . '-' . substr($dt_final, 3, 2) . '-' . substr($dt_final, 0, 2);
        $marca = MarcaProduto::findOne($marca_id)->nome;
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->spreadsheet->getProperties()->setCreator('OPT Soluções')
            ->setTitle("Relatorio Estoque ($marca)")
            ->setCreator('OPT Soluções');

        $sqlEstoque = $this->getSqlEstoque($marca_id);
        $sqlVendas = $this->getSqlVendas($marca_id, $dt_inicial, $dt_final);
        $this->setRelatorio($sqlEstoque, $sqlVendas);

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=Estoque_" . $marca . ".xlsx");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 2030 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function setRelatorio($sql, $vendas)
    {

        $count = count($sql);

        $styleHead = [
            'font' => [
                'bold'  =>  true,
                'size'  =>  10,
                'name'  =>  'Verdana'
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $this->sheet->getStyle('A1:M1')->applyFromArray($styleHead);

        $styleBody = [
            'font' => [
                'bold'  =>  false,
                'size'  =>  8,
                'name'  =>  'Verdana'
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ];

        $this->setCabecalho();

        $linha = 2;
        foreach ($sql as $query) {
            $this->sheet->getStyle('A' . $linha . ':K' . $linha)->applyFromArray($styleBody);
            $this->spreadsheet->getActiveSheet()->setCellValue('A' . $linha, $query['produto_id']);
            $this->spreadsheet->getActiveSheet()->setCellValue('B' . $linha, $query['codigo_global']);
            $this->spreadsheet->getActiveSheet()->setCellValue('C' . $linha, $query['nome']);
            $this->spreadsheet->getActiveSheet()->setCellValue('D' . $linha, $query['filial']);
            $this->spreadsheet->getActiveSheet()->setCellValue('E' . $linha, $query['quantidade']);
            foreach ($vendas as $queryVenda) {
                if ($queryVenda['pa'] == $query['produto_id']) {
                    $this->spreadsheet->getActiveSheet()->setCellValue('F' . $linha, $queryVenda['vendidos']);
                }
            }
            $this->spreadsheet->getActiveSheet()->setCellValue('G' . $linha, $query['valor_compra']);
            $this->spreadsheet->getActiveSheet()->setCellValue('H' . $linha, $query['valor']);
            $this->spreadsheet->getActiveSheet()->setCellValue('I' . $linha, $query['codigo_fabricante']);
            $this->spreadsheet->getActiveSheet()->setCellValue('J' . $linha, $query['marca_produto']);
            $this->spreadsheet->getActiveSheet()->setCellValue('K' . $linha, $query['marca']);

            $linha++;
        }
    }

    public function setCabecalho()
    {
        $this->spreadsheet->getActiveSheet()->setCellValue('A1', 'ID Prod.');
        $this->spreadsheet->getActiveSheet()->setCellValue('B1', 'Código Global');
        $this->spreadsheet->getActiveSheet()->setCellValue('C1', 'Nome');
        $this->spreadsheet->getActiveSheet()->setCellValue('D1', 'Filial');
        $this->spreadsheet->getActiveSheet()->setCellValue('E1', 'Qtd. Estoque');
        $this->spreadsheet->getActiveSheet()->setCellValue('F1', 'Qtd. Vendida');
        $this->spreadsheet->getActiveSheet()->setCellValue('G1', 'Val. Compra');
        $this->spreadsheet->getActiveSheet()->setCellValue('H1', 'Valor');
        $this->spreadsheet->getActiveSheet()->setCellValue('I1', 'Código Fabricante');
        $this->spreadsheet->getActiveSheet()->setCellValue('J1', 'Marca Produto');
        $this->spreadsheet->getActiveSheet()->setCellValue('K1', 'Marca');
    }

    public function getSqlEstoque($marca)
    {

        $sql = "select 
        codigo_global as codigo_global,
		codigo_fabricante as codigo_fabricante,
		nome as nome,
		marca_produto as marca_produto,
	    codigo_similar as codigo_similar,
		aplicacao_complementar as aplicacao_complementar,
		produto_id as produto_id,
		valor as valor,
		marca as marca,
		valor_compra as valor_compra,
		quantidade as quantidade,
        filial as filial
	    from (
		    select	
            produto_filial.filial_id,
			produto_filial.id,
			produto_filial.produto_id as produto_id,
			marca_produto as marca_produto,
			valor_produto_filial.valor as valor,
			marca_produto as marca,
            filial.nome as filial,
			valor_produto_filial.valor_compra as valor_compra,
			produto_filial.quantidade,
			replace(replace(produto.nome,';',''),'','''''') as nome,
			produto.codigo_global as codigo_global,
			produto.codigo_similar as codigo_similar,
			produto.codigo_fabricante as codigo_fabricante,
			produto.aplicacao_complementar as aplicacao_complementar,
			rank() over(partition by produto_filial.id order by dt_inicio desc) as ranking
		    from	produto_filial
			left join produto on produto.id = produto_filial.produto_id
			left join filial on produto_filial.filial_id = filial.id
			left join valor_produto_filial on valor_produto_filial.produto_filial_id = produto_filial.id
			left join marca_produto on produto.marca_produto_id = marca_produto.id
			where filial.id in (93, 94, 95, 96) and marca_produto.id = " . $marca . ") as foo
                where ranking = 1
                order by codigo_global";

        return $query = Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function getSqlVendas($marca, $dt_inicial, $dt_final)
    {

        $sql = "select * from (
        select
            produto.id as pa,
            count(pedido_produto_filial_cotacao.quantidade) as vendidos
        from
            pedido_produto_filial_cotacao
        inner join pedido_produto_filial on
            pedido_produto_filial.id = pedido_produto_filial_cotacao.pedido_produto_filial_id
        inner join pedido on
            pedido.id = pedido_produto_filial.pedido_id
        inner join produto_filial on
            produto_filial.id = pedido_produto_filial_cotacao.produto_filial_id
        inner join produto on
            produto.id = produto_filial.produto_id
        inner join marca_produto on
            marca_produto.id = produto.marca_produto_id
        where
            (pedido.dt_referencia between '$dt_inicial' and '$dt_final')
            and
    produto.marca_produto_id = $marca
        group by
            produto.id           
    union
        select
            produto.id as pa,
            count(pedido_mercado_livre_produto_produto_filial.quantidade) as vendidos
        from
            pedido_mercado_livre_produto_produto_filial
        inner join pedido_mercado_livre_produto on
            pedido_mercado_livre_produto.id = pedido_mercado_livre_produto_produto_filial.pedido_mercado_livre_produto_id
        inner join pedido_mercado_livre on
            pedido_mercado_livre.id = pedido_mercado_livre_produto.pedido_mercado_livre_id
        inner join produto_filial on
            produto_filial.id = pedido_mercado_livre_produto_produto_filial.produto_filial_id
        inner join produto on
            produto.id = produto_filial.produto_id
        inner join marca_produto on
            marca_produto.id = produto.marca_produto_id
        where
            (pedido_mercado_livre.date_created between '$dt_inicial' and '$dt_final')
            and
    produto.marca_produto_id = $marca
        group by
            produto.id) as foo";

        return $query = Yii::$app->db->createCommand($sql)->queryAll();
    }
}
