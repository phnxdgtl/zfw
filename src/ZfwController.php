<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

use Carbon\Carbon;

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

    /**
     * Handle the posted form data
     *
     * @param Request $request
     * @param string $form The name of the form
     * @return void
     */
    public function formHandler(Request $request, $form) {

        $this->validateForm($request, $form);
        $id = $this->saveFormData($request, $form);
        /**
         * Old, non-GDPR code:
         * $this->emailFormData($request, $form);
         */
        $this->emailFormDataGDPR($request, $form, $id);
        return $this->backToThanks();
    }

    protected function backToThanks() {
        $thanks = 'thanks';

        /**
         * Surely we could/should redirect to a route named {$form}-thanks here?
         */

        $previous = URL::previous();
        if (substr($previous,0 - strlen($thanks)) == $thanks) {
            $back = $previous;
        }
        else {
            $back = rtrim($previous,'/').'/thanks';
        }
        return redirect($back);
    }

    /**
     * Validate the Form
     *
     * @param Request $request
     * @param string $form The name of the form
     * @return void
     */
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

    /**
     * Save form data to the database
     *
     * @param Request $request
     * @param string $form The name of the form
     * @return integer $id The ID of the database record created
     */
    protected function saveFormData($request, $form) {
        $tableName = "zfw_".$form;
        if (!Schema::hasTable($tableName)) {
            trigger_error("Database table doesn't exist for form $form");
            return false;
        }

        $insertData = [
            'created_at'=>Carbon::now('Europe/London')->toDateTimeString()
        ];

        $fields = $this->getFormConfig($form,'fields');
        foreach ($fields as $fieldName=>$data) {
            if (Schema::hasColumn($tableName, $fieldName)) {
                $insertData[$fieldName] = $this->formatValueFromRequest($request, $form, $fieldName);
            }
        }

        $id = DB::table($tableName)->insertGetId(
            $insertData
        );

        return $id;

     }

     /**
      * Email data from a form to the recipient stated in the config file
      * NOTE: this is not GDPR compliant. I'm really leaving this here for
      * reference only. See: @emailFormDataGDPR
      *
      * @param [type] $request
      * @param [type] $form
      * @return void
      */
     protected function emailFormData($request, $form) {

        $emailData = [];

        $to      = $this->getFormConfig($form, 'to');
        $fields  = $this->getFormConfig($form, 'fields');
        $subject = $this->getFormConfig($form, 'subject');

        foreach ($fields as $fieldName=>$data) {
            $value = $this->formatValueFromRequest($request, $form, $fieldName);

            if (!$value) continue; // This is right, I think? Don't show a label for an empty value?

            /**
             * Also, if we haven't specified a label, don't include this in the email. This makes it easy to exclude fields from the email.
             */
            if (empty($data->label)) continue;

            $labelDelimiter = substr($data->label,-1) == '?' ? '': ':';
            $string = "**{$data->label}{$labelDelimiter}** ";
            if ($data->type == 'textarea') {
                $string = "\n$string \n*$value*\n\n";
            }
            else if ($data->type == 'checkbox' && in_array($value,[1,0,'No','Yes'])) {
                if ($value === 1 || $value === '1' || $value === 'Yes') {
                    $string .= 'Yes';
                } else {
                    $string .= 'No';
                }
            }
            else {
                $string .= "$value";
            }
            $emailData[] = $string;
        }

        $message = implode("  \n",$emailData);

        Mail::to($to)->send(new ZfwNotification($message, $subject));
     }

     /**
      * Email data from a form to the recipient stated in the config file
      * in a GDPR-compliant way; ie, a link to the CMS.
      *
      * @param Request $request
      * @param string $form
      * @param integer $id The ID of the saved form in the database
      * @return void
      */
      protected function emailFormDataGDPR($request, $form, $id) {

        $emailData = [];

        $to      = $this->getFormConfig($form,'to');
        $subject = $this->getFormConfig($form, 'subject');

        /**
         * If we don't set a "to" in the config, don't send an email
         */
        if (!$to) return false;

        $fields = $this->getFormConfig($form,'fields');

        foreach ($fields as $fieldName=>$data) {
            $value = $this->formatValueFromRequest($request, $form, $fieldName);

            if (!$value) continue; // This is right, I think? Don't show a label for an empty value?

            /**
             * Also, if we haven't specified a label, don't include this in the email.
             * This makes it easy to exclude fields from the email.
             */
            if (empty($data->label)) continue;

            $labelDelimiter = substr($data->label,-1) == '?' ? '': ':';
            $string = "**{$data->label}{$labelDelimiter}** ";

            if ($data->type == 'textarea') {
                /**
                 * Add some spaces around a longer "message"-type field
                 */
                $string = "\n$string \n*$value*\n\n";
            }
            else if ($data->type == 'checkbox' && in_array($value,[1,0])) {
                $string .= $value ? 'No' : 'Yes';
            }
            else {
                $string .= "$value";
            }

            $emailData[] = $string;
        }

        $message = implode("  \n",$emailData);

        $tableName   = "zfw_".$form;
        $ctrlClassId = DB::table('ctrl_classes')->where('table_name', $tableName)->value('id');

        if (empty($ctrlClassId)) {
            trigger_error("CtrlClass doesn't exist for form $form");
            return false;
        }

        /**
         * Can/should we check for an existing route here?
         * Something like Route::has('route.name');
         */

        $gdprLink    = route('ctrl::view_object',[
                            'ctrl_class_id' => $ctrlClassId,
                            'object_id'     => $id
                        ]);
        $message .= "\n\n[Click here to view this message]($gdprLink)";

        Mail::to($to)->send(new ZfwNotification($message, $subject));
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
        if ($type == 'checkbox') {
            if (is_array($value)) {
                $value = implode(', ',$value);
            }
            else if (is_null($value)) {
                $value = 0;
            }
        }
        return $value;
    }

    protected function getFormConfig($form,$key = NULL) {
        $config = json_decode(config('zfw.forms'));
        if (is_null($config)) {
            trigger_error("Config file doesn't exist for form $form, or isn't valid");
            return false;
        }
        if ($key) {
            return $config->$form->$key ?? false;
        }
        else {
            return $config->$form;
        }

    }
}
