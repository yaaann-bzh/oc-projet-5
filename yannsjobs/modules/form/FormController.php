<?php


namespace yannsjobs\modules\form;

use framework\HTTPRequest;
use framework\Controller;


class FormController extends Controller {
    public function executeGetStandards(HTTPRequest $request) {
        $inputs = explode(',', $request->getData('inputs'));
        
        $standards = $this->app->config()->getFormConfigJSON('inputs', $inputs);

        $json = json_encode($standards);
        $this->page->setContent($json);
    }
    
    public function executeExistsControl(HTTPRequest $request) {
        $manager = $this->managers->getManagerOf(ucfirst($request->getData('manager')));
        $column = $request->getData('column');
        $value = $request->getData('value');
        
        try {
            $id = $manager->getId($column, $value);
        } catch (\Exception $exc) {
            return $this->app->httpResponse()->redirect404();
        }
        
        if(!$id){
            return $this->app->httpResponse()->redirect404();
        } else {
            return $this->app->httpResponse()->send204();
        }
    }
}
