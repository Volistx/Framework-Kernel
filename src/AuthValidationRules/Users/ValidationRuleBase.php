<?php

namespace Volistx\FrameworkKernel\AuthValidationRules\Users;

use Illuminate\Http\Request;

abstract class ValidationRuleBase
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract public function Validate(): bool|array;
}