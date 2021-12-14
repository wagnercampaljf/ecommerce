<?php

namespace common\models;

use common\tecnickcom\tcpdf\tcpdf;

class Padraopdf extends TCPDF
{

    private $pdf;

    public function Header($title = '')
    {
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, 40, $title, 'www.pecaagora.com', array(0, 0, 0), array(0, 0, 0));
        $this->pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    }

    public function Footer()
    {
        $this->pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
        $this->pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    }

    public function setInit($orietation = 'P', $title = '')
    {
        $this->pdf = new TCPDF($orietation);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Peca Agora');
        $this->pdf->SetTitle($title);
        $this->Header($title);
        $this->Footer();

        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(10, PDF_MARGIN_TOP, 10);
        $this->pdf->SetAutoPageBreak(TRUE, 18);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->pdf->SetFont('Times', '', 10, '', true);

        return $this->pdf;
    }
}
