<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidEmailDomains implements Rule
{
    public function passes($attribute, $value)
    {

        foreach ($value as $domain) {
            if ($domain == "") {
                continue;
            }
            
            if (!$this->isValidDomain($domain)) {
                return false;
            }
        }

        return true;
    }

    protected function isValidDomain($domain)
    {
        if (strpos($domain, '@') !== 0) {
            return false;
        }
        // Extract domain from email format (e.g., @domain.com)
        $domain = substr($domain, 1);

        // Check if the domain contains '@' and validate using filter_var
        return (bool) preg_match('/^(?:[a-z0-9](?:[a-z0-9-æøå]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/isu', $domain);
    }

    public function message()
    {
        return 'One or more domains are not valid.';
    }
}
