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
    
    public function isValid(HTTPRequest $request, Manager $manager = null) {
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
            
            if ($input['uniq'] AND !empty($value)){
                $this->checkExists($manager, $key, $value);
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
            $this->errors[$key] = 'Le champ "' . $key . '" doit avoir au moins ' . $input['min'] . ' caractères';
        } elseif (strlen($value) > $input['max']) {
            $this->errors[$input] = 'Le champ "' . $key . '" ne doit pas dépasser ' . $input['max'] . ' caractères';
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function intCheck($key, array $input, $value) {
        if ((int)$value < $input['min']) {
            $this->errors[$key] = '"' . $key . '" doit être supérieur ou égale à ' . $input['min'];
        } elseif ((int)$value > $input['max']) {
            $this->errors[$key] = '"'. $key . '" doit être inférieur ou égal à ' . $input['max'];
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function loginCheck($key, array $input, string $value) {
        $keyMod = substr($key, 0, -5);
        if (strlen($value) < $input['min'] OR strlen($value) > $input['max']) {
            $this->errors[$keyMod] = "Identifiant ou mot de passe incorrect";
        } else {
            $this->values[$keyMod] = $value;
        }
    }
    
    public function phoneCheck($key, array $input, string $value) {
        if(!empty($value)) {
            if (!preg_match($input['regex'], $value)) {
                $this->errors[$key] = 'Numéro de téléphone invalide';
            } else {
                $this->values[$key] = $value;
            }
        }
    }
    
    public function emailCheck($key, array $input, string $value) {
        if (strlen($value) > $input['max']) {
            $this->errors[$input] = 'Le champ "' . $key . '" ne doit pas dépasser ' . $input['max'] . ' caractères';
        } elseif (!preg_match($input['regex'], $value)) {
            $this->errors[$key] = 'Adresse mail non valide';
        } else {
            $this->values[$key] = $value;
        }
    }

    public function passCheck($key, array $input, string $value) {
        if (strlen($value) > $input['max']) {
            $this->errors[$input] = 'Le champ "' . $key . '" ne doit pas dépasser ' . $input['max'] . ' caractères';
        } elseif (!preg_match($input['regex'], $value)) {
            $this->errors['pass_secu'] = 'Votre mot de passe doit respecter les critères de sécurité';
        } else {
            $this->values[$key] = $value;
        }
    }
    
    public function confirmCheck($key, array $input, string $value) {
        if (!isset($this->values['pass'])){
            $this->errors['pass_same'] = 'Problème dans la comparaison des mots de passe';
        } elseif ( $this->values['pass'] !== $value) {
            $this->errors['pass_same'] = 'Les mots de passe doivent être identiques';
        } else {
            $this->values['pass'] = password_hash($this->values['pass'], PASSWORD_DEFAULT);
        }
    }
    
    public function checkboxCheck($key, array $input, string $value) {
        if ($input['required'] AND empty($value)){
            $this->errors[] = 'Le champ "' . $key . '" est requis pour ce formulaire.';
        }
    }

    public function fileCheck($key, array $input, array $file) {
        if (!empty($file)) {  
            if ($file['error'] !== 0) {
                $this->errors[$key] = 'Erreur upload : ' . $file['error'];
            } 

            if ($file['size'] > $input['max'] * 1024) {
                $this->errors[$key] = 'La taille pour le fichier "'. $key . '" doit être inférieur ou égal à ' . $input['max'] . ' ko';
            } elseif ($file['size'] < $input['min'] * 1024) {
                $this->errors[$key] = 'La taille pour le fichier "'. $key . '" semble faible (<' . $input['max'] . ' ko), vérifier qu\'il n\'y a pas d\'erreur';
            } 

            if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $input['ext'])){
                $this->errors[$key] = 'L\'extension du fichier n\'est pas valide';
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

        $this->errors['password'] = 'Identifiant ou mot de passe incorrect';
        return null;
    }
    
    public function checkExists(Manager $manager, string $key, string $value) {
        $id = $manager->getId($key, $value);
        if ($id){
            $this->errors[$key] = 'Il existe déjà une entrée ' . $value;
        }
    }
    
    public function setValues(string $key, $value) {
        $this->values[$key] = $value;
    }
    
    public function setErrors(string $key, $message) {
        $this->errors[$key] = $message;
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
