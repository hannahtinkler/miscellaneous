<?php

namespace Tests\Unit\Services;

use Tests\Unit\TestCase;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use App\Services\ResponsiveImageGenerator;
use App\Exceptions\InvalidOptionsException;

class ResponsiveImageGeneratorTest extends TestCase
{
    public function testItCanGetOptions()
    {
        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $expected = [
            'sizes' => [
                [
                    'width' => 1024,
                    'height' => null
                ],
                [
                    'width' => 640,
                    'height' => null
                ],
                [
                    'width' => 320,
                    'height' => null
                ],
            ],
            'breakpoints' => [
                '100vw',
            ],
            'manipulations' => [],
            'attributes' => [],
        ];

        $actual = $generator->getOptions();

        $this->assertEquals($expected, $actual);
    }
    
    public function testItCanSetOptions()
    {
        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $expected = [
            'sizes' => [
                [
                    'width' => 1000,
                    'height' => null,
                ],
                [
                    'width' => 2000,
                    'height' => null,
                ],
            ],
            'breakpoints' => [
                'media-query' => 'min-width: 36rem',
                'width' => '33vw'
            ],
            'manipulations' => [],
            'attributes' => [],
        ];

        $generator->setOptions($expected);

        $actual = $generator->getOptions();

        $this->assertEquals($expected, $actual);
    }
    
    public function testItThrowsWhenBadSizesArePassed()
    {
        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $this->expectException(InvalidOptionsException::class);

        $generator->checkOptionsAreValid([
            'sizes' => [
                [
                    'width' => null,
                    'height' => null,
                ],
            ],
        ]);
    }
    
    public function testItThrowsWhenBadBreakpointsArePassed()
    {
        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $this->expectException(InvalidOptionsException::class);

        $generator->checkOptionsAreValid([
            'sizes' => [
                [
                    'width' => 500,
                    'height' => null,
                ],
            ],
            'breakpoints' => [
                '',
            ],
        ]);
    }
    
    public function testItCanGetExtraAttributes()
    {
        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $image = $this->mock(Image::class);
        $image->width()->willReturn(1000);
        $image->height()->willReturn(500);

        $expected = ' style="width: 50%" class="image"';
        
        $actual = $generator->getExtraAttributes([
            'style' => 'width: 50%',
            'class' => 'image',
        ]);


        $this->assertEquals($expected, $actual);
    }
    
    public function testItCanGetFileName()
    {
        $imagePath = 'https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg';

        $generator = new ResponsiveImageGenerator(
            $this->mock(ImageManager::class)->reveal()
        );

        $image = $this->mock(Image::class);
        $image->width()->willReturn(1000);
        $image->height()->willReturn(500);

        $fileName = $generator->getFileName($imagePath, $image->reveal());

        $expected = 1;
        $actual = preg_match('/\/amazing-beautiful-beauty-blue-1000x500-\w{32}\.jpg/', $fileName);


        $this->assertEquals($expected, $actual);
    }
}
