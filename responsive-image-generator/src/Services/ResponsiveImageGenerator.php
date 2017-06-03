<?php

namespace App\Services;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use App\Exceptions\InvalidOptionsException;

class ResponsiveImageGenerator
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var array
     */
    private $sourceSets = [];

    /**
     * @var array
     */
    private $options = [
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
            '100vw'
        ],
        'manipulations' => [],
        'attributes' => [],
    ];
    
    /**
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager, $directory = '')
    {
        $this->imageManager = $imageManager;
        $this->directory = $directory;
    }

    /**
     * @param array $options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        $this->checkOptionsAreValid($this->options);
    }

    /**
     * @param  array $options
     * @return void
     */
    public function checkOptionsAreValid(array $options)
    {
        foreach ($options['sizes'] as $size) {
            if (!isset($size['width']) && !isset($size['height'])) {
                throw new InvalidOptionsException("Please specify a height and/or width for every size specification");
            }
        }

        foreach ($options['breakpoints'] as $breakpoint) {
            if (!$breakpoint) {
                throw new InvalidOptionsException("All breakpoints must have content. Alternatively, pass in an empty array.");
            }
        }
    }

    /**
     * @param  string $imagePath
     * @return string
     */
    public function generate($imagePath, $altText)
    {
        $this->saveResizedImages($imagePath);

        return sprintf(
            '<img src="%s"
                 srcset="%s"
                 sizes="%s"
                 alt="%s"%s
             >',
            $imagePath,
            implode(', ', $this->sourceSets),
            implode(', ', $this->options['breakpoints']),
            $altText,
            $this->getExtraAttributes($this->options['attributes'])
        );
    }

    /**
     * @param  string $imagePath
     * @return void
     */
    public function saveResizedImages($imagePath)
    {
        foreach ($this->options['sizes'] as $details) {
            $image = $this->imageManager->make($imagePath);

            if ($details['width'] && $details['height']) {
                $image->fit($details['width'], $details['height']);
            } elseif ($details['width']) {
                $image->widen($details['width']);
            } else {
                $image->heighten($details['height']);
            }

            $image = $this->processManipulations($image);

            $fileName = $this->getFileName($imagePath, $image);

            $image->save($fileName);

            $this->sourceSets[] = sprintf('%s %sw', $fileName, $image->width());
        }
    }

    /**
     * @param  Image  $image
     * @return Image
     */
    public function processManipulations(Image $image)
    {
        foreach ($this->options['manipulations'] as $manipulation => $parameters) {
            $image->$manipulation(...$parameters);
        }

        return $image;
    }

    public function getExtraAttributes($attributeConfig)
    {
        $attributes = '';

        foreach ($attributeConfig as $key => $value) {
            $attributes .= sprintf(' %s="%s"', $key, $value);
        }

        return $attributes;
    }

    /**
     * Generate a path with a unique identifier for this file
     * @param  string $imagePath
     * @param  Image  $image
     * @return string
     */
    public function getFileName($imagePath, Image $image)
    {
        $details = pathinfo($imagePath);

        $identifier = md5(serialize($this->options));

        return sprintf(
            '%s/%s-%dx%d-%s.%s',
            $this->directory,
            $details['filename'],
            $image->width(),
            $image->height(),
            $identifier,
            $details['extension']
        );
    }
}
