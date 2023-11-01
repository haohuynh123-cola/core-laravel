<?php

namespace App\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class CustomDataSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
       return $data;
    }

    public function item($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }
}
