<?php

namespace Awjudd\AssetProcessor\Asset;

use Awjudd\AssetProcessor\Processor\Processor;

abstract class Asset
{
    /**
     * Whether or not the file is JavaScript.
     *
     * @var bool
     */
    protected $_isJavaScript = false;

    /**
     * Whether or not the file is a StyleSheet.
     *
     * @var bool
     */
    protected $_isStyleSheet = false;

    /**
     * Derives the metadata that is required for the asset.
     */
    abstract protected function deriveMetadata();

    /**
     * Retrieves the public path for the asset.
     *
     * @return string Public path.
     */
    abstract public function getPublicPath();

    /**
     * Retrieves the file extension for the asset.
     *
     * @return string
     */
    abstract public function getExtension();

    /**
     * Processes the asset.
     *
     * @return Asset The updated asset object
     */
    abstract public function process();

    /**
     * Retrieves the name of the asset file.
     *
     * @return string Name.
     */
    abstract public function getName();

    /**
     * Retrieves the file's full name.
     *
     * @return string
     */
    abstract public function getFullName();

    /**
     * Creates an array of assets.
     *
     * @param string $path
     */
    public static function create($path)
    {
        throw new NotImplementedException();
    }

    /**
     * Determines if the processor handles style sheet.
     *
     * @return bool True if style sheet processor, False otherwise.
     */
    public function isStylesheet()
    {
        return (bool) $this->_isStyleSheet;
    }

    /**
     * Determines if the asset is a JavaScript file.
     *
     * @return bool True if JavaScript file, False otherwise.
     */
    public function isJavaScript()
    {
        return (bool) $this->_isJavaScript;
    }

    /**
     * Retrieves the HTML required for a stylesheet.
     *
     * @param array $attributes Any extra attributes to provide
     *
     * @return string The HTML to emit
     */
    public function stylesheet(array $attributes = [])
    {
        // Are we a stylesheet?
        if (!$this->isStylesheet()) {
            // We aren't, so we are done
            return '';
        }

        return sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" %s />',
            $this->getPublicPath(),
            $this->deriveAttributes($attributes)
        );
    }

    /**
     * Retrieves the HTML required for a JavaScript.
     *
     * @param array $attributes Any extra attributes to provide
     *
     * @return string The HTML to emit
     */
    public function javascript(array $attributes = [])
    {
        // Are we a JavaScript file?
        if (!$this->isJavaScript()) {
            // We aren't, so we are done
            return '';
        }

        return sprintf(
            '<script type="text/javascript" src="%s" %s></script>',
            $this->getPublicPath(),
            $this->deriveAttributes($attributes)
        );
    }

    /**
     * Derives the key-value pair of attributes.
     *
     * @param  array  $attributes The attributes to include
     *
     * @return string The HTML to emit for any attributes.
     */
    private function deriveAttributes(array $attributes)
    {
        $text = '';

        // Loop through any attributes
        foreach ($attributes as $key => $value) {
            $text .= sprintf(
                '%s="%s" ',
                $key,
                htmlentities($value)
            );
        }

        return $text;
    }
}
