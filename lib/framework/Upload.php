<?php

namespace framework;

class Upload {
    
    protected $name;
    protected $type;
    protected $tmp_name;
    protected $error;
    protected $size;
    
    public function __construct(array $donnees = [])
    {
        if (!empty($donnees))
        {
            $this->hydrate($donnees);
        }
    }
    
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $attribut => $valeur)
        {
            $methode = 'set'.ucfirst($attribut);

            if (is_callable([$this, $methode]))
            {
                $this->$methode($valeur);
            }
        }
    }
    
    public function save(string $path,string $prefix = 'file_',string $id = null, bool $overwrite = false) {
        if (empty($this->name)){
            return ;
        }
        
        $ext = substr($this->name, strrpos($this->name, '.')+1);
        
        if (!file_exists($path)) {
            throw new \Exception('Le chemin spécifié pour l\'enregistrement du fichier n\'existe pas');
        }
        
        $this->name = $path . '/' . $prefix . $id . '.' . $ext;
        
        if (file_exists($this->name) AND $overwrite === false){
            throw new \Exception('Il existe déjà un fichier portant ce nom');
        }
        
        move_uploaded_file($this->tmp_name, $this->name);
        
    }
    
    public function setName($name)
    {
        if (!is_string($name) || empty($name))
        {
            throw new \Exception('Nom du fichier invalide invalide');
        }

        $this->name = $name;
    }

    public function setType($type)
    {
        if (!is_string($type) || empty($type))
        {
            throw new \Exception('Type de fichier invalide');
        }

        $this->type = $type;
    } 
    
    public function setTmp_name($tmp_name)
    {
        if (!is_string($tmp_name) || empty($tmp_name))
        {
            throw new \Exception('Fichier temporaire invalide');
        }

        $this->tmp_name = $tmp_name;
    } 
    
    public function setError($error)
    {
        $this->error = $error;
    }
    
    public function setSize($size)
    {
        if (!is_int($size) || empty($size))
        {
            throw new \Exception('Taille de fichier invalide');
        }

        $this->size = $size;
    }
    
    public function name() {
        return $this->name;
    }
    
    public function type() {
        return $this->type;
    } 
    
    public function tmp_name() {
        return $this->tmp_name;
    }
    
    public function error() {
        return $this->error;
    }
    
    public function size() {
        return $this->size;
    }
}
