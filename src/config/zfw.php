<?php

/**
 * This returns a JSON encoded string for 'forms', as per the example below
 */

return [
	'forms' => '',
];

/**
 * Example JSON config:
 *
{
    "contact": {
        "to": "",
        "fields": {
                "name": {
                    "type":         "text",
                    "label":        "Name",
                    "validation":   "required"
                },
                "email": {
                    "type":         "email",
                    "label":        "Email address",
                    "validation":   "required|email"
                }
            }
        }
    }
}
 *
 */