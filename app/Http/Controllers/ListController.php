<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriberList;
use App\Http\Resources\ListResource;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    
    public function index()
    {
        return ListResource::collection(Auth::user()->lists());
    }

    public function show(SubscriberList $list)
    {
        $this->authorize('view', $list);

        return ListResource::make($list);
    }

    public function destroy(SubscriberList $list)
    {
        $this->authorize('delete', $list);

        $list->delete();

        return 200;
    }

}
