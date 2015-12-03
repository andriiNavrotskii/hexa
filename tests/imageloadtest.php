<?php
require 'image_load.php';


class imageLoadTest extends PHPUnit_Framework_TestCase
{

	public $image_jpg = 'http://www.instrument.in.ua/images/product/21149m.jpg';
	public $image_png = 'http://www.instrument.in.ua/images/mobile.png';
	public $image_gif = 'http://www.instrument.in.ua/images/bx_loader.gif';
	public $not_image = 'http://static.specialized.com/media/docs/support/0000047554/0000047554.pdf';
	public $image_empty;

	public $path_true = 'path';
	public $path_false = 'pathhhhhh';
	public $path_empty;

    /**
     * @dataProvider provider
     */
	public function testNormal($a, $b, $c) {

		$loader = new imageLoad($c);

		$this->assertEquals($a, $loader->go($b));

	}



	public function provider() {
		return array(
			array(true, $this->image_jpg, $this->path_true),
			array(true, $this->image_gif, $this->path_true),
			array(true, $this->image_png, $this->path_true),
			array(false, $this->not_image, $this->path_true),
			array(false, $this->image_empty, $this->path_true),

			array(false, $this->image_jpg, $this->path_false),
			array(false, $this->image_gif, $this->path_false),
			array(false, $this->image_png, $this->path_false),
			array(false, $this->not_image, $this->path_false),
			array(false, $this->image_empty, $this->path_false),

			array(false, $this->image_jpg, $this->path_empty),
			array(false, $this->image_gif, $this->path_empty),
			array(false, $this->image_png, $this->path_empty),
			array(false, $this->not_image, $this->path_empty),
			array(false, $this->image_empty, $this->path_empty),

		);
	}



	public function testSetRewriteOn() {

        $this->assertAttributeEquals(
          true,  
          'rewrite', 
          new imageLoad($path_true) 
        );

	}






}
