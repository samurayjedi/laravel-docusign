<?php

namespace Samurayjedi\LaravelDocusign\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Exception;

class JWTController {
  /**
   * DocuSign login handler
   * @param Request
   */
  function handle(Request $request) {
    // Check given state against previously stored one to mitigate CSRF attack
    if ($request->has('code') && $request->filled('code')) {
      // we have obtained consent, let's shortcut and login the user
      $returnUrl = session('ds_return_url');
      $url = URL::isValidUrl($returnUrl)
        ? $returnUrl
        : route('dashboard');
      session()->forget('ds_return_url');
      return redirect($url);
    }

    throw new Exception('Invalid JWT state');
  }
}
