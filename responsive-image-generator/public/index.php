<?php

require __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use App\Services\ResponsiveImageGenerator;


$generator = new ResponsiveImageGenerator(
    new ImageManager(['driver' => 'gd']),
    'images/'
);

$standardExample = $generator->generate(
    'https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg',
    'Pretty photo'
);


$generator = new ResponsiveImageGenerator(
    new ImageManager(['driver' => 'gd']),
    'images/'
);

$generator->setOptions([
    'attributes' => [
        'style' => 'width: 50%',
        'class' => 'image',
    ],
]);

$attributesExample = $generator->generate(
    'https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg',
    'Pretty photo'
);


$generator = new ResponsiveImageGenerator(
    new ImageManager(['driver' => 'gd']),
    'images/'
);

$generator->setOptions([
    'manipulations' => [
        'blur' => [60],
        'rotate' => [180],
        'pixelate' => [10],
    ],
]);

$manipulationExample = $generator->generate(
    'https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg',
    'Pretty photo'
);


$generator = new ResponsiveImageGenerator(
    new ImageManager(['driver' => 'gd']),
    'images/'
);

$generator->setOptions([
    'sizes' => [
        [
            'width' => 600,
            'height' => 600,
        ],
        [
            'width' => 300,
            'height' => 300,
        ],
        [
            'width' => 100,
            'height' => 100,
        ],
    ],
]);

$sizesExample = $generator->generate(
    'https://static.pexels.com/photos/36478/amazing-beautiful-beauty-blue.jpg',
    'Pretty photo'
);

?>

<h1>Standard Config Example</h1>
<p>Using the default config with specifies width only.</p>
<?= $standardExample ?>

<h1 style="margin-top: 2rem">Custom Size Config Example</h1>
<p>Utilising fit() to get an image of the correct size without stretching or distorting the image.</p>
<?= $sizesExample ?>

<h1 style="margin-top: 2rem">Manipulation Config Example</h1>
<p>Applying blur(), rotate() and pixelate() as examples, though any valid method can be used</p>
<?= $manipulationExample ?>
