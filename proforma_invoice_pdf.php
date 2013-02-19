<?php
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2010-11-20
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */
include('config.php');
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');
require('tcpdf/htmlcolors.php');
include('simplehtmldom_1_5/simple_html_dom.php');

$pi_id=$_REQUEST[pi_id];
$client_id=$_REQUEST[client_id];
$request_type=$_REQUEST[request_type];

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
/*
 $pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
*/
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 8);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//

/*
 *	Parses HTML from the output of /proforma_invoice_add.php
*/
$filepath=$scriptpath."/proforma_invoice_add.php?pi_id=$pi_id&param=view&client_id=$client_id&passcode=12345";
//echo $filepath;
$html=file_get_html($filepath);

$e=$html->find("form", 0);
foreach($e->find('script') as $temp)
	$temp->outertext = '';

foreach($e->find('textarea') as $temp)
	$temp->outertext = $temp->innertext;

foreach($e->find('input[type=text]') as $temp)
	$temp->outertext = $temp->value;

$e->find('a[class=button]',0)->outertext='';
$str=$e->innertext;
$pdf->writeHTML($str, true, false, true, false, '');

/*************************************************************/


$pdf->lastPage();

// ---------------------------------------------------------

$pdf_file_name=$company_shortcode."_PI_".$pi_id."_".time().".pdf";
//Close and output PDF document
if($request_type=='download'){
	$pdf->Output($pdf_file_name, 'I');
}else if($request_type=='email'){
	$pdf->Output("temp/".$pdf_file_name, 'F');
	header("location:proforma_invoice_pdf_email.php?client_id=$client_id&pi_id=$pi_id&file=$pdf_file_name");
}

//============================================================+
// END OF FILE
//============================================================+
