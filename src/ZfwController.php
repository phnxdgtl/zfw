<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

use DB;
use URL;
use Mail;

class ZfwController extends Controller
{
    public function test($thanks = NULL) {

        return view('zfw::test-form',[
            'thanks' => $thanks
        ]);
    }

    public function formHandler(Request $request, $form) {
        $this->validateForm($request, $form);
        $this->saveFormData($request, $form);
        $this->emailFormData($request, $form);
        return $this->backToThanks();
    }

    protected function backToThanks() {
        $thanks = 'thanks';
        $previous = URL::previous();
        if (substr($previous,0 - strlen($thanks)) == $thanks) {
            $back = $previous;
        }
        else {
            $back = "$previous/thanks";
        }
        return redirect($back);
    }

    protected function validateForm($request, $form) {

        $validationRules = [];

        $fields = $this->getFormConfig($form,'fields');

        foreach ($fields as $fieldName=>$data) {
            if ($rules = $data->validation) {
                $validationRules[$fieldName] = $rules;
            }
        }

        $validatedData = $request->validate($validationRules);
    }

    protected function saveFormData($request, $form) {
        $tableName = "zfw_".$form;
        if (!Schema::hasTable($tableName)) {
            trigger_error("Database table doesn't exist for form $form");
            return false;
        }

        $insertData = [];

        $fields = $this->getFormConfig($form,'fields');
        foreach ($fields as $fieldName=>$data) {
            if (Schema::hasColumn($tableName, $fieldName)) {
                $insertData[$fieldName] = $this->formatValueFromRequest($request, $form, $fieldName);
            }
        }

        DB::table($tableName)->insert(
            $insertData
        );

     }

     /**
      * Email data from a form to the recipient stated in the config file
      *
      * @param [type] $request
      * @param [type] $form
      * @return void
      */
     protected function emailFormData($request, $form) {

        $emailData = [];

        $to     = $this->getFormConfig($form,'to');
        $fields = $this->getFormConfig($form,'fields');

        foreach ($fields as $fieldName=>$data) {
            $value = $this->formatValueFromRequest($request, $form, $fieldName);
            $labelDelimiter = substr($data->label,-1) == '?' ? '': ':';
            $string = "**{$data->label}{$labelDelimiter}** ";
            if ($data->type == 'textarea') {
                $string = "\n$string \n*$value*\n\n";
            }
            else if ($data->type == 'checkbox') {
                $string .= $value ? 'No' : 'Yes';
            }
            else {
                $string .= "$value";
            }
            $emailData[] = $string;
        }

        $message = implode("  \n",$emailData);

        Mail::to($to)->send(new ZfwNotification($message));
     }

    /**
     * Format a posted value before adding to the database (depending on its type, for example)
     *
     * @param Request $request
     * @param string $form
     * @param string $fieldName
     * @return void
     */
    protected function formatValueFromRequest($request, $form, $fieldName) {
        $value  = $request->input($fieldName);
        $field  = $this->getFormConfig($form,'fields');
        $type   = $field->$fieldName->type;
        if ($type == 'checkbox' && is_null($value)) {
            $value = 0;
        }
        return $value;
    }

    protected function getFormConfig($form,$key = NULL) {
        $config = json_decode(config('zfw.forms'));
        if (is_null($config)) {
            trigger_error("Config file doesn't exist for form $form");
            return false;
        }
        if ($key) {
            return $config->$form->$key;
        }
        else {
            return $config->$form;
        }

    }
}
