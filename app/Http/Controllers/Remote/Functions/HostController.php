<?php

namespace App\Http\Controllers\Remote\Functions;

use App\Models\Host;
use App\Actions\HostAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class HostController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);
        $hosts = Host::thisUser()->get();
        return $this->success($hosts);
    }

    public function store(Request $request)
    {

        $hostAction = new HostAction();

        $host = $hostAction->create($request->all());

        return $this->created($host);
    }

    public function show(Request $request, Host $host)
    {
        $this->isUser($host);

        return $this->success($host);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Host $host)
    {
        // 排除 request 中的一些参数
        $request_only = $request->except(['id', 'user_id', 'host_id', 'price', 'managed_price', 'suspended_at', 'created_at', 'updated_at']);

        $hostAction = new HostAction();

        $host = $hostAction->update($host, $request_only);

        return $this->updated($host);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Host $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        // 具体删除逻辑

        dispatch(new \App\Jobs\HostJob($host, 'destroy'));

        return $this->deleted($host);
    }


    public function isUser(Host $host)
    {
        // return $host->user_id == Auth::id();

        if (request('user_id') !== null) {
            if ($host->user_id != request('user_id')) {
                abort(403);
            }
        }
    }
}
