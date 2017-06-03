<?php

namespace Tests\Functional\Services;

use Intervention\Image\ImageManager;
use App\Services\ResponsiveImageGenerator;

class ResponsiveImageGeneratorTest extends \Codeception\Test\Unit
{
    private $directory = __DIR__ . '/../../_data/images';

    public function _before()
    {
        foreach (glob($this->directory . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testItCanGenerateImageCodeWithDefaultOptions()
    {
        $generator = new ResponsiveImageGenerator(
            new ImageManager(['driver' => 'gd']),
            $this->directory
        );

        $expected = '<img src="https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg"
                 srcset="C:\Sites\mrb\responsive-image-generator\tests\functional\Services/../../_data/images/amazing-beautiful-beauty-blue-1024x668-d3b47011f4be58cdf15995bf54975002.jpg 1024w, C:\Sites\mrb\responsive-image-generator\tests\functional\Services/../../_data/images/amazing-beautiful-beauty-blue-640x417-d3b47011f4be58cdf15995bf54975002.jpg 640w, C:\Sites\mrb\responsive-image-generator\tests\functional\Services/../../_data/images/amazing-beautiful-beauty-blue-320x209-d3b47011f4be58cdf15995bf54975002.jpg 320w"
                 sizes="100vw"
                 alt="Pretty photo"
             >';

        $actual = $generator->generate('https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg', 'Pretty photo');

        $this->assertEquals($expected, $actual);
    }
    
    public function testItCanGenerateImageCodeWithCustomOptions()
    {
        $generator = new ResponsiveImageGenerator(
            new ImageManager(['driver' => 'gd']),
            $this->directory
        );

        $generator->setOptions([
            'sizes' => [
                [
                    'width' => 1000,
                    'height' => 500
                ],
                [
                    'width' => null,
                    'height' => 500
                ],
            ],
            'breakpoints' => [
                '100vw',
                '(min-width: 32rem) 33vw',
            ],
            'attributes' => [
                'class' => 'image',
                'style' => 'width: 50%',
            ],
        ]);

        $expected = '<img src="https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg"
                 srcset="C:\Sites\mrb\responsive-image-generator\tests\functional\Services/../../_data/images/amazing-beautiful-beauty-blue-1000x500-256e31f757d4723a45f05fa750092203.jpg 1000w, C:\Sites\mrb\responsive-image-generator\tests\functional\Services/../../_data/images/amazing-beautiful-beauty-blue-767x500-256e31f757d4723a45f05fa750092203.jpg 767w"
                 sizes="100vw, (min-width: 32rem) 33vw"
                 alt="Pretty photo" class="image" style="width: 50%"
             >';

        $actual = $generator->generate('https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg', 'Pretty photo');

        $this->assertEquals($expected, $actual);
    }
    
    public function testItCanSaveResizedImages()
    {
        $generator = new ResponsiveImageGenerator(
            new ImageManager(['driver' => 'gd']),
            $this->directory
        );

        $generator->saveResizedImages('https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg');

        $this->assertTrue(file_exists($this->directory . '/amazing-beautiful-beauty-blue-1024x668-d3b47011f4be58cdf15995bf54975002.jpg'));
        $this->assertTrue(file_exists($this->directory . '/amazing-beautiful-beauty-blue-640x417-d3b47011f4be58cdf15995bf54975002.jpg'));
        $this->assertTrue(file_exists($this->directory . '/amazing-beautiful-beauty-blue-320x209-d3b47011f4be58cdf15995bf54975002.jpg'));
    }
}
