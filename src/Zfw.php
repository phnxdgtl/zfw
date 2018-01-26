<?php

namespace Sevenpointsix\Zfw;

class Zfw {

    /**
     * This is the class that's exposed by the Facade, so we can call methods directly
     */

     /**
      * Render the JS that the form needs; this should be added as {!! Zfw::renderJS() !!}
      *
      * @return string
      */
    public function renderJS()
    {
        return view('zfw::js');
    }

    /**
     * Return a string containing all the attributes a ZFW <form> tag requires
     *
     * @param string $formName
     * @return void
     */
    public function formAttributes($formName) {
        $attributes = array();
        $attributes['method'] = 'post';
        $attributes['class'] = 'zfw';
        $attributes['action'] = route('zfw',['form' => $formName]);

        /**
         * OK, this is a bit convoluted, could have just looped $attributes
         */

        array_walk($attributes,function(&$item) {
            $item = "\"$item\"";
        });

        return urldecode(http_build_query($attributes,'',' '));
    }

    /**
     * Some short functions; I can't decide on the best naming convention here...
     */
    public function js() {
        return $this->renderJS();
    }
    public function form($formName) {
        return $this->formAttributes($formName);
    }

}