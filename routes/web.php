<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dev helper: run a few DB queries to collect profiling info (only in local)
if (app()->environment('local')) {
    Route::get('/__debug_queries', function () {
        $users = \Illuminate\Support\Facades\DB::select('select id, email from users order by id desc limit 5');
        $count = \Illuminate\Support\Facades\DB::select('select count(*) as c from users');
        return response()->json(['users' => $users, 'count' => $count[0]->c ?? 0]);
    });

    Route::get('/__debug_n_plus_one', function () {
        // simulate N+1: fetch clientes and for each load cobrancas count (without eager loading)
        $clientes = App\Models\Cliente::limit(20)->get();
        $counts = [];
        foreach ($clientes as $c) {
            $counts[] = $c->cobrancas()->count();
        }
        return response()->json(['clientes' => $clientes->pluck('id'), 'counts_sample' => array_slice($counts,0,5)]);
    });

    Route::get('/__debug_n_plus_one_eager', function () {
        // same but with eager loading
        $clientes = App\Models\Cliente::with('cobrancas')->limit(20)->get();
        $counts = [];
        foreach ($clientes as $c) {
            $counts[] = $c->cobrancas->count();
        }
        return response()->json(['clientes' => $clientes->pluck('id'), 'counts_sample' => array_slice($counts,0,5)]);
    });
}
