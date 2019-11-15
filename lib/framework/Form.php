<?php

namespace framework;

class Form{

    protected $inputs;
    protected $values = [];
    protected $errors = [];


    public function __construct(array $inputs) {
                
        $this->inputs = $inputs;
    }
    
    public function isValid(HTTPRequest $request) {
        foreach ($this->inputs as $key => $input) {
            if($request->postExists($key)) {  
                $methode = $input['type'] . 'Check';
                $value = $request->postData($key);
            }
            
            if ($input['required'] AND empty($value)){
                $this->errors[] = 'Le champ "' . $key . '" est requis pour ce formulaire.';
            }
            
            if (is_callable([$this, $methode])) {
                $this->$methode($key, $input, $value);
            } else {
                $this->errors[] = 'Methode de contrôle inconnue pour le champ ' . $key . '.';
            }
        }
        
        if(empty($this->errors)) {
            return true;
        }
        
        return false;
    }
    
    public function textCheck($key, array $input, string $value) {
        if (strlen($value) < $input['min']) {
            $this->errors[] = 'Le champ "' . $key . '" doit avoir au moins ' . $input['min'] . ' caractères';
        } elseif (strlen($value) > $input['max']) {
            $this->errors[] = 'Le champ "' . $key . '" ne doit pas dépasser ' . $input['max'] . ' caractères';
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function intCheck($key, array $input, $value) {
        if ((int)$value < $input['min']) {
            $this->errors[] = '"' . $key . '" doit être supérieur ou égale à ' . $input['min'];
        } elseif ((int)$value > $input['max']) {
            $this->errors[] = '"'. $key . '" doit être inférieur ou égal à ' . $input['max'];
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function inputs() {
        return $this->inputs;
    }
    
    public function values() {
        return $this->values;
    }
    
    public function errors() {
        return $this->errors;
    }
}
