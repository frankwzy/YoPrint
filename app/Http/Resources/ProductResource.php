<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'style' => $this->style,
            'sanmar' => $this->sanmar_mainframe_color,
            'size' => $this->size,
            'color' => $this->color_name,
            'price' => number_format((float) $this->piece_price, 2, '.', ''),
        ];
    }
}
