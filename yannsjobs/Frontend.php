<?php
namespace yannsjobs;

use framework\Application;

class Frontend extends Application
{
    protected $name = 'Frontend';

    public function run()
    {
        $this->userConnect();

        $controller = $this->getController();
        $controller->execute();
      
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}
