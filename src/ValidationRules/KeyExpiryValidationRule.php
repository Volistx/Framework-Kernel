<?php

namespace VolistxTeam\VSkeletonKernel\ValidationRules;

use Carbon\Carbon;
use VolistxTeam\VSkeletonKernel\Facades\Messages;

class KeyExpiryValidationRule extends ValidationRuleBase
{
    public function Validate(): bool|array
    {
        $token = $this->inputs['token'];

        if ($token->expires_at && Carbon::now()->greaterThan(Carbon::createFromTimeString($token->expires_at))) {
            return [
                'message' => Messages::E403(),
                'code'    => 403,
            ];
        }

        return true;
    }
}
