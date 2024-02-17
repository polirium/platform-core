<?php

namespace Polirium\Core\Base\Http\Requests;

use Polirium\Core\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'username' => 'required|min:4',
            'password' => 'required|string|min:6|max:60',
        ];
    }
}
