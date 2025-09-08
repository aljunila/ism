<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendaftaranResource extends JsonResource
{
     //define properti
    public $resource;

    /**
     * __construct
     *
     * @param  mixed $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * toArray
     *
     * @param  mixed $request
     * @return array
     */

    public function toArray(Request $request)
    {
        // return parent::toArray($request);
         return $this->resource;
    }
}
