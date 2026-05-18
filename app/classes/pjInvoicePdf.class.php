<?php
if (!defined('ROOT_PATH')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

/**
 * TCPDF-subclass voor factuur PDF's met een aangepaste Footer() override.
 * Plaatst de bedrijfs-footer onderaan elke pagina, ongeacht body-lengte.
 *
 * Gebruik:
 *   $pdf = new pjInvoicePdf(...);
 *   $pdf->setInvoiceFooterHtml($footerHtml);
 *   $pdf->setPrintFooter(true);
 *   $pdf->setFooterMargin(15);
 *   $pdf->SetAutoPageBreak(true, 28);
 *   $pdf->AddPage();
 *   $pdf->writeHTML($body);
 *   $pdf->Output($filename, 'S');
 */
class pjInvoicePdf extends TCPDF
{
    /** @var string HTML van de footer */
    protected $invoiceFooterHtml = '';

    /**
     * Stel de footer-HTML in (wordt onderaan elke pagina gerenderd).
     */
    public function setInvoiceFooterHtml($html)
    {
        $this->invoiceFooterHtml = (string) $html;
        return $this;
    }

    /**
     * Override van TCPDF::Footer() — wordt door TCPDF aangeroepen
     * bij elke nieuwe pagina als setPrintFooter(true) is gezet.
     */
    public function Footer()
    {
        if (empty($this->invoiceFooterHtml)) {
            return;
        }
        // Plaats footer ~22mm boven de bottom-edge
        $this->SetY(-22);
        $this->writeHTMLCell(0, 0, '', '', $this->invoiceFooterHtml, 0, 0, 0, true, '', true);
    }
}
