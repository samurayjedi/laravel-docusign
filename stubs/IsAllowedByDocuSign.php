<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Samurayjedi\LaravelDocusign\Facades\JWTAuthFacade;

class IsAllowedByDocuSign {
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next) {
    // if (JWTAuth::isTokenInvalid())
    $oauthProvider = new JWTAuthFacade;
    $code = $oauthProvider->login();
    if ($code === 'consent_required') {
      // we found consent_required in the response body meaning first time consent is needed
      $provider = $oauthProvider->get_oauth_provider();
      $authorizationURL = $provider->getAuthorizationUrl([
        'scope' => "impersonation+" . $oauthProvider->getScope(),
        'redirect_uri' => route('docusign-oauth-jwt'),
        'client_id' => env('DS_CLIENT_ID', ''),
        'state' => $provider->getState(),
        'response_type' => 'code'
      ]);
      session(['ds_return_url' => $request->url()]);

      return redirect("https://$authorizationURL");
    } else {
      $oauthProvider->createSessionVars();
    }

    return $next($request);
  }
}
