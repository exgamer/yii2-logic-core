<?php

namespace concepture\yii2logic\helpers;

use Yii;

/**
 * Class XmlHelper
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

    public static function dd()
    {
        $doc = new \DOMDocument();
        $doc->loadXML($sitemap->content);
        $parent = $doc->getElementsByTagName('urlset')->item(0);
        $url = $doc->createElement("url");
        $loc = $doc->createElement("loc", "https://legalbet.ru/best-posts/zhelto-sinij-trend-v-otbore-evro-2016/");
        $priority = $doc->createElement("priority", "0.5");
        $url->appendChild($loc);
        $url->appendChild($priority);
        $parent->appendChild($url);
    }
}