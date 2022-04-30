<?php

namespace Volistx\FrameworkKernel\UserAuthValidationRules;

use Volistx\FrameworkKernel\Enums\AccessRule;
use Volistx\FrameworkKernel\Facades\GeoLocation;
use Volistx\FrameworkKernel\Facades\Messages;

class CountryValidationRule extends ValidationRuleBase
{
    public function Validate(): bool|array
    {
        $token = $this->inputs['token'];
        $request = $this->inputs['request'];

        if ($token->country_rule === AccessRule::NONE) {
            return true;
        }

        $country = GeoLocation::search($request->getClientIp())->country;

        if (($token->country_rule === AccessRule::BLACKLIST && in_array($country, $token->country_range)) ||
            ($token->country_rule === AccessRule::WHITELIST && !in_array($country, $token->country_range))) {
            return [
                'message' => Messages::E403('The application is not allowed to access from your country.'),
                'code'    => 403,
            ];
        }

        return true;
    }
}
