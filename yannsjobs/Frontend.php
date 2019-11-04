<?php
namespace yannsjobs;

use framework\Application;

class Frontend extends Application
{
    protected $name = 'Frontend';

    public function run()
    {
        $controller = $this->getController();
        $controller->execute();
      
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}
