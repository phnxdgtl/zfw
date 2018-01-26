<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Console\Command;
use Sunra\PhpSimple\HtmlDomParser;

class ZfwCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zfw {route?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check a site for forms and plug them in to ZFW';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $route = $this->argument('route');

        if (!$route) {
            $this->error("TODO: scrape routes file for all known routes");
        }
        else {
            $this->checkFormFromRoute($route);
        }
    }

    /**
     * Load a page from a named route, attempt to indentify a form on it
     * and then run various checks and updates accordingly
     *
     * @param string $route
     * @return void
     */
    protected function checkFormFromRoute($route) {
        $this->comment("Loading page from route $route");
        // Is there a form on this page? Will we need to add the "zfw" class to each form first? I think so, otherwise we'll be forever having to skip search forms etc
        $form = $this->loadFormFromPage($route);
        $this->comment("There ".($form ? 'IS' : 'IS NOT')." a form on this page");

        // Generate a config file for this form if we don't have one
        // Generate a database table for this form if we don't have one (via migrations)
        // Check to see if the database table has all fields, and update if not (via migration)
        // Check to make sure that we have a valid /thanks route for the form
    }

    /**
     * Do we have a form.zfw on the page that this route loads?
     *
     * @param string $route
     * @return boolean
     */
    protected function loadFormFromPage($route) {
        $this->error("TODO: Looking for form on page from route $route");

        $file = file_get_contents(route($route));
        /**
         * Not entirely sure why we can't pass the URL directly to HtmlDomParser::file_get_html
         * but we get a "stream does not support seeking" error if we do
         */

        $html = HtmlDomParser::str_get_html($file);

        // Find all images
        foreach ($html->find('form.zfw') as $form) {
            return $form;
            /**
             * Would we ever have two ZFW forms on one page?
             * Unlikely, but this code could be modified to support it
             */
        }

        return false;

    }
}
