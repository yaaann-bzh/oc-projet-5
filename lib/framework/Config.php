<?php
namespace framework;

class Config extends ApplicationComponent
{
    protected $vars = [];

    public function get($fileName, $var)
    {
        if (!isset($this->vars[$var]))
        {
            $xml = new \DOMDocument;
            $xml->load(__DIR__.'/../../config/' . $fileName . '.xml');
            
            $elements = $xml->getElementsByTagName('define');

            foreach ($elements as $element)
            {
                $this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
            }
        }

        if (isset($this->vars[$var]))
        {
            return $this->vars[$var];
        }

        return null;
    }
}