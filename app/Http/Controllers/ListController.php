<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriberList;
use App\Http\Resources\ListResource;

class ListController extends Controller
{
    
    public function show(SubscriberList $list)
    {
        $this->authorize('view', $list);

        return ListResource::make($list);
    }

}
