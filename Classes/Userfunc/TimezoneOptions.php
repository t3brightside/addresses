<?php

namespace Brightside\Addresses\Userfunc;

class TimezoneOptions {
    /**
     * Get available timezones and populate the dropdown options
     *
     * @param array $config TCA config array
     * @return void
     */
    public function getTimezones(array &$config) {
        $timezones = \DateTimeZone::listIdentifiers();
        foreach ($timezones as $timezone) {
            // Add each timezone as an option in the dropdown
            $config['items'][] = [$timezone, $timezone];
        }
    }
}
