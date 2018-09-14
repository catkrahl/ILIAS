<?php
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilXlsFoParserTest extends PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$formData = array(
			'certificate_text' => '<xml> Some Context </xml>',
			'margin_body' => array(
				'top'    => '1cm',
				'right'  => '2cm',
				'bottom' => '3cm',
				'left'   => '4cm'
			),
			'pageformat' => 'a4'
		);

		$settings = $this->getMockBuilder('ilSetting')
			->disableOriginalConstructor()
			->getMock();

		$settings->method('get')
			->willReturn('Something');

		$pageFormats = $this->getMockBuilder('ilPageFormats')
			->disableOriginalConstructor()
			->getMock();

		$pageFormats->method('fetchPageFormats')
			->willReturn(array(
				'a4' => array(
					'name' => 'A4', // (297 mm x 210 mm)
					'value' => 'a4',
					'width' => '210mm',
					'height' => '297mm'
				),
			));

		$xmlChecker = $this->getMockBuilder('ilXMLChecker')
			->getMock();

		$utilHelper = $this->getMockBuilder('ilCertificateUtilHelper')
			->getMock();

		$utilHelper->method('stripSlashes')
			->willReturnOnConsecutiveCalls(
				'1cm',
				'2cm',
				'3cm',
				'4cm'
			);

		$xlstProcess = $this->getMockBuilder('ilCertificateXlstProcess')
			->getMock();

		$xlstProcess->method('process')
			->with(
				array(
					'/_xml' => '<html><body><xml> Some Context </xml></body></html>',
					'/_xsl' => '<xml>Some XLS Content</xml>'
				),
				array(
					'pageheight'      => '297mm',
					'pagewidth'       => '210mm',
					'backgroundimage' => '[BACKGROUND_IMAGE]',
					'marginbody'      => implode(' ', array(
						'1cm',
						'2cm',
						'3cm',
						'4cm'
					))
				)
			)
			->willReturn('Something Processed');

		$language = $this->getMockBuilder('ilLanguage')
			->disableOriginalConstructor()
			->getMock();

		$certificateXlsFileLoader = $this->getMockBuilder('ilCertificateXlsFileLoader')
			->getMock();

		$certificateXlsFileLoader->method('getXlsCertificateContent')
			->willReturn('<xml>Some XLS Content</xml>');

		$xlsFoParser = new ilXlsFoParser(
			$settings,
			$pageFormats,
			$xmlChecker,
			$utilHelper,
			$xlstProcess,
			$language,
			$certificateXlsFileLoader
		);

		$output = $xlsFoParser->parse($formData);

		$this->assertEquals('Something Processed', $output);
	}
}
