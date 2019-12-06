<?php

namespace framework;

class Form{

    protected $inputs;
    protected $values = [];
    protected $errors = [];
    protected $files = [];


    public function __construct(array $inputs, array $values = []) {
                
        $this->inputs = $inputs;
        $this->values = $values;
    }
    
    public function isValid(HTTPRequest $request, Manager $manager = null, int $userId = 0) {
        foreach ($this->inputs as $key => $input) {
            $methode = '';
            if($request->postExists($key)) {  
                $methode = str_replace('Login', '', $input['type']) . 'Check';
                $value = $request->postData($key);
            } elseif($request->fileExists($key)){
                $methode = $input['type'] . 'Check';
                $value = $request->fileData($key)['size'] !== 0? $request->fileData($key): [];
            }

            if ($input['required'] AND empty($value)){
                $this->errors[] = 'Le champ "' . $key . '" est requis pour ce formulaire.';
            }
            
            if ($input['uniq'] AND !empty($value)){
                $this->checkExists($manager, $key, $value, $userId);
            }

            if (is_callable([$this, $methode])) {
                $this->$methode($key, $input, $value);            
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
            $this->errors[$key] = 'Le champ "' . $key . '" ne doit pas dépasser ' . $input['max'] . ' caractères';
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
        $keyMod = str_replace('Login', '', $key);
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
        } else {
            $this->values[$key] = null;
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
    
    public function checkExists(Manager $manager, string $key, string $value, int $userId) {
        $id = $manager->getId($key, $value, $userId);
        if ($id){
            $this->errors[$key] = 'Il existe déjà une entrée ' . $value;
        }
    }
    
    public function getValues($entity) {
        foreach ($this->inputs as $input => $standards) {
            if(is_callable([$entity, $input])) {
                $this->values[$input] = $entity->$input();
            }
        }
    }
    
    public function setValues(string $key, $value) {
        $this->values[$key] = $value;
    }
    
    public function unsetValues(string $key) {
        unset($this->values[$key]);
    }
    
    public function setErrors(string $key, $message) {
        if(empty($key)) {
            $this->errors[] = $message;
        } else {
            $this->errors[$key] = $message;
        }      
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
