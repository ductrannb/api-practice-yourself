<?php

namespace App\Repositories;

use App\Models\Otp;

class OtpRepository extends BaseRepository
{
    public function __construct(Otp $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }
}
