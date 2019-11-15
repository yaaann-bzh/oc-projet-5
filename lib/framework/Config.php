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
    
    public function getFormConfig($fileName, array $inputs) {
        
        foreach ($inputs as $input) {
            if (!isset($this->vars[$input]))
            {
                $xml = new \DOMDocument;
                $xml->load(__DIR__.'/../../config/' . $fileName . '.xml');

                $elements = $xml->getElementsByTagName('define');

                foreach ($elements as $element)
                {
                    $this->vars[$element->getAttribute('input')] = array(
                        'required' => $element->getAttribute('required'),
                        'type' => $element->getAttribute('type'),
                        'max' => $element->getAttribute('max'),
                        'min' => $element->getAttribute('min'),
                        );       
                }
            }
            
            if (isset($this->vars[$input]))
            {
                $config[$input] = $this->vars[$input];
            }
        }
        
        if (isset($config)) {
            return $config;
        }

        return null;
    }
}