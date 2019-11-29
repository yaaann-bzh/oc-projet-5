<?php

namespace framework;

class Files {

    protected $filesNames = [];
    
    public function __construct(array $filesNames) {
        $this->filesNames = $filesNames;
    }
    
    public function delete() {
        foreach ($this->filesNames as $file) {
            if(file_exists($file)) {
                unlink($file);
            }  
        }

    }
    
}
