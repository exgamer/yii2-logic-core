<?php

namespace concepture\yii2logic\helpers;

use Yii;
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class ClassHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class XmlHelper
{
    /**
     * @param $dom
     * @param $data
     * @return bool
     */
    public static function generateXmlElement( \DOMDocument $dom, array $data ) {
        if ( empty( $data['name'] ) )
            return false;

        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );

        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }

        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;

            $child = static::generateXmlElement( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }

        return $element;
    }
}