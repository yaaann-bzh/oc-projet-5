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
}
