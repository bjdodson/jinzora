<?php
if (!defined(JZ_SECURE_ACCESS))
	die('Security breach detected.');
/**
* Creates a PDF of the cover of the album
*
* @author Ross Carlson
* @since 6/20/05
* @version 6/20/05
* @param $node The node that we are viewing
**/
global $config_version, $node;

// Did they want to create it?
if (isset ($_GET['sub_action'])) {
	if ($_GET['sub_action'] == "create") {
		// Ok, let's create the PDF
		// Code borrowed from Netjukebox - www.netjukebox.nl
		$album = $node->getName();
		$artnode = $node->getAncestor("artist");
		$artist = $artnode->getName();

		$pdf = pdf_new();
		pdf_open_file($pdf, '');

		pdf_set_info($pdf, 'Title', $artist . " - " . $album);
		pdf_set_info($pdf, 'Creator', 'Jinzora ' . $config_version);

		pdf_begin_page($pdf, 595, 842); //A4
		$scale = 2.834645676; //mm to dtp-point (1 point = 1/72 inch)
		pdf_scale($pdf, $scale, $scale);
		pdf_setlinewidth($pdf, .1);

		//  +---------------------------------------------------------------------------+
		//  | PDF Back Cover                                                            |
		//  +---------------------------------------------------------------------------+
		$x0 = 30;
		$y0 = 22;
		pdf_translate($pdf, $x0, $y0);

		pdf_moveto($pdf, 0, -1);
		pdf_lineto($pdf, 0, -11);
		pdf_moveto($pdf, 6.5, -1);
		pdf_lineto($pdf, 6.5, -11);
		pdf_moveto($pdf, 144.5, -1);
		pdf_lineto($pdf, 144.5, -11);
		pdf_moveto($pdf, 151, -1);
		pdf_lineto($pdf, 151, -11);
		pdf_moveto($pdf, 0, 119);
		pdf_lineto($pdf, 0, 129);
		pdf_moveto($pdf, 6.5, 119);
		pdf_lineto($pdf, 6.5, 129);
		pdf_moveto($pdf, 144.5, 119);
		pdf_lineto($pdf, 144.5, 129);
		pdf_moveto($pdf, 151, 119);
		pdf_lineto($pdf, 151, 129);
		pdf_moveto($pdf, -11, 0);
		pdf_lineto($pdf, -1, 0);
		pdf_moveto($pdf, -11, 118);
		pdf_lineto($pdf, -1, 118);
		pdf_moveto($pdf, 152, 0);
		pdf_lineto($pdf, 162, 0);
		pdf_moveto($pdf, 152, 118);
		pdf_lineto($pdf, 162, 118);
		pdf_stroke($pdf);

		$temp = '';
		// Now let's get the tracks for this album
		$tracks = $node->getSubNodes("tracks", -1);
		foreach ($tracks as $track) {
			$meta = $track->getMeta();
			$temp .= "         " . $meta['number'] . " - " . $track->getName() . "\n";
		}

		$font = pdf_findfont($pdf, 'Helvetica', 'winansi', 0);
		pdf_setfont($pdf, $font, 3);
		pdf_show_boxed($pdf, $temp, 6.5, 0, 138, 108, 'left', '');

		pdf_setfont($pdf, $font, 4);
		pdf_set_text_pos($pdf, 2, -4.5); //y,-x
		pdf_rotate($pdf, 90);
		pdf_show($pdf, $artist . ' - ' . $album);
		pdf_rotate($pdf, -90);

		pdf_setfont($pdf, $font, 4);
		pdf_set_text_pos($pdf, -116, 151 - 4.5); //-y,x
		pdf_rotate($pdf, -90);
		pdf_show($pdf, $artist . ' - ' . $album);
		pdf_rotate($pdf, 90);

		//  +---------------------------------------------------------------------------+
		//  | PDF Front Cover                                                           |
		//  +---------------------------------------------------------------------------+
		$x0 = 44 - $x0;
		$y0 = 160 - $y0;
		pdf_translate($pdf, $x0, $y0);

		pdf_moveto($pdf, 0, -1);
		pdf_lineto($pdf, 0, -11);
		pdf_moveto($pdf, 121, -1);
		pdf_lineto($pdf, 121, -11);
		pdf_moveto($pdf, 0, 121);
		pdf_lineto($pdf, 0, 131);
		pdf_moveto($pdf, 121, 121);
		pdf_lineto($pdf, 121, 131);
		pdf_moveto($pdf, -1, 0);
		pdf_lineto($pdf, -11, 0);
		pdf_moveto($pdf, -1, 120);
		pdf_lineto($pdf, -11, 120);
		pdf_moveto($pdf, 122, 0);
		pdf_lineto($pdf, 132, 0);
		pdf_moveto($pdf, 122, 120);
		pdf_lineto($pdf, 132, 120);
		pdf_stroke($pdf);

		// Do we have album art?
		if ($node->getMainArt() <> false) {
			$extension = substr(strrchr($node->getMainArt(), '.'), 1);
			$extension = strtolower($extension);
			if ($extension == 'jpg')
				$pdfdfimage = pdf_open_image_file($pdf, 'jpeg', $node->getMainArt(), '', 0);
			if ($extension == 'png')
				$pdfdfimage = pdf_open_image_file($pdf, 'png', $node->getMainArt(), '', 0);
			if ($extension == 'gif')
				$pdfdfimage = pdf_open_image_file($pdf, 'gif', $node->getMainArt(), '', 0);
			$sx = 121 / pdf_get_value($pdf, 'imagewidth', $pdfdfimage);
			$sy = 120 / pdf_get_value($pdf, 'imageheight', $pdfdfimage);

			pdf_scale($pdf, $sx, $sy);
			pdf_place_image($pdf, $pdfdfimage, 0, 0, 1);
		}

		//  +---------------------------------------------------------------------------+
		//  | Close PDF                                                                 |
		//  +---------------------------------------------------------------------------+
		pdf_end_page($pdf);
		pdf_close($pdf);
		$buffer = pdf_get_buffer($pdf);
		$file = $artist . ' - ' . $album . '.pdf';
		header('Content-Type: application/force-download');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename="' . $file . '"'); //rawurlencode not needed for header

		echo $buffer;
		pdf_delete($pdf);
		exit ();
	}
}

$this->displayPageTop("", word("Create PDF Cover"));
$this->openBlock();

$dlarr = array ();
$dlarr['action'] = "popup";
$dlarr['ptype'] = "pdfcover";
$dlarr['sub_action'] = "create";
$dlarr['jz_path'] = $node->getPath("string");

echo "<center>To generate the PDF cover for the album click below<br><br><br>";
echo '<a href="' . urlize($dlarr) . '">Generate PDF</a>';

$this->closeBlock();
?>
