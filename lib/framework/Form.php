<?php

namespace framework;

class Form{

    protected $inputs;
    protected $values = [];
    protected $errors = [];
    protected $files = [];


    public function __construct(array $inputs) {
                
        $this->inputs = $inputs;
    }
    
    public function isValid(HTTPRequest $request) {
        foreach ($this->inputs as $key => $input) {
            if($request->postExists($key)) {  
                $methode = $input['type'] . 'Check';
                $value = $request->postData($key);
            } elseif($request->fileExists($key)){
                $methode = $input['type'] . 'Check';
                $value = $request->fileData($key)['size'] !== 0? $request->fileData($key): [];
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
    
    public function loginCheck($key, array $input, string $value) {
        if (strlen($value) < $input['min'] OR strlen($value) > $input['max']) {
            $this->errors[] = "Identifiant ou mot de passe incorrect";
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function fileCheck($key, array $input, array $file) {
        if (!empty($file)) {  
            if ($file['error'] !== 0) {
                $this->errors[] = 'Erreur upload : ' . $file['error'];
            } 

            if ($file['size'] > $input['max'] * 1024) {
                $this->errors[] = 'La taille pour le fichier "'. $key . '" doit être inférieur ou égal à ' . $input['max'] . ' ko';
            } elseif ($file['size'] < $input['min'] * 1024) {
                $this->errors[] = 'La taille pour le fichier "'. $key . '" semble faible (<' . $input['max'] . ' ko), vérifier qu\'il n\'y a pas d\'erreur';
            } 

            if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $input['ext'])){
                $this->errors[] = 'L\'extension du fichier n\'est pas valide';
            }   
        }
        
        if(empty($this->errors)) {
            $this->files[$key] = new Upload($file);
        }  
    }
    
    public function checkPassword(Manager $manager) {

        $member = null;
        
        if (isset($this->values['email']) AND isset($this->values['pass'])){
            $email = $this->values['email'];
            $pass = $this->values['pass'];
            $member = $manager->getSingle($manager->getId('email', $email));            
        }
        
        if ($member !== null) {
            if ($member->deleteDate() !== null) {
                $this->errors[] = 'Ce compte utilisateur a été supprimé';
                return null;
            }
            if (password_verify($pass, $member->pass())) {
                return $member;
            }
        } 

        $this->errors[] = 'Identifiant ou mot de passe incorrect';
        return null;
    }
    
    public function inputs() {
        return $this->inputs;
    }
    
    public function values() {
        return $this->values;
    }
    
    public function files() {
        return $this->files;
    }
    
    public function file(string $key) {
        return $this->files[$key];
    }
    
    public function errors() {
        return $this->errors;
    }
}
