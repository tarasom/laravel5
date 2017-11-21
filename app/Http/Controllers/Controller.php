<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthorizationService;
use App\Services\Exchange\ExchangeService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function showProfile()
    {
        if (null === $accessToken = session()->get('access_token')) {
            return redirect()->route('main');
        }

        $authService = new AuthorizationService();
        $user = $authService->getUser($accessToken);

        $exchangeService = new ExchangeService();
        $exchangeService->authorize($user);

        return view('profile')->with([
            'user'  => $user,
            'rates' => $exchangeService->getCurrencyPairRates(),
        ]);
    }
}
