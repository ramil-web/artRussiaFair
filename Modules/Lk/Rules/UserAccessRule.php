<?php

namespace Lk\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserAccessRule implements Rule
{


    public function __construct(public int $id, public string $model )
    {
    }

    public function passes($attribute, $value): bool
    {
        $model = app("App\Models\\$this->model");
        $query = $model->query()->findOrFail($this->id);
        return Auth::check() && Auth::user()->userApplications()->where('id', $query->user_application_id)->exists();
    }

    public function message(): string
    {
        return 'Текущий пользователь не имеет доступа к указанному ресурсу.';
    }
}
