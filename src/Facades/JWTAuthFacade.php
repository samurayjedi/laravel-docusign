<?php

namespace Samurayjedi\LaravelDocusign\Facades;

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use Samurayjedi\LaravelDocusign\Providers\DocuSignProvider;
use Throwable;

class JWTAuthFacade {
  const TOKEN_REPLACEMENT_IN_SECONDS = 600; # 10 minutes
  protected static $expires_in;
  protected static $access_token;
  protected static $expiresInTimestamp;
  protected static $account;
  protected static ApiClient $apiClient;
  
  /**
   * Checker for the JWT token
   */
  public static function isTokenInvalid() {
    $token = session('ds_access_token', null);
    $expirationTime = session('ds_expiration', 0);
    return is_null($token)
      || (time() + self::TOKEN_REPLACEMENT_IN_SECONDS) > (int) $expirationTime;
  }

  public function __construct() {
    $config = new Configuration();
    self::$apiClient = new ApiClient($config);
  }

  /**
   * DocuSign login handler
   * @throws \DocuSign\eSign\Client\ApiException
   */
  public function login() {
    $token = $this->configureJwtAuthorizationFlowByKey();
    if ($token === 'consent_required') {
      return $token;
    }
    self::$access_token = $token;
    self::$expiresInTimestamp = time() + self::$expires_in;

    if (is_null(self::$account)) {
      self::$account = self::$apiClient->getUserInfo(self::$access_token->getAccessToken());
    }
    return self::$access_token;
  }

  /**
   * Get JWT auth by RSA key
   */
  private function configureJwtAuthorizationFlowByKey() {
    self::$apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTHORIZATION_SERVER', ''));
    $privateKey = file_get_contents(
      storage_path('app/docusign/' . env('DS_PRIVATE_KEY_FILE', '')),
      true
    );
    try {
      $response = self::$apiClient->requestJWTUserToken(
        $aud = env('DS_CLIENT_ID', ''),
        $aud = env('DS_IMPERSONATED_USER_ID', ''),
        $aud = $privateKey,
        $aud = $this->getScope()
      );
      return $response[0];    //code...
    } catch (Throwable $th) {
      if (strpos($th->getMessage(), 'consent_required') !== false) {
        return 'consent_required';
      }
      throw new \Exception($th->getMessage());
    }
  }
  
  public function getScope() {
    return 'signature';
  }
  
  /**
   * Get OAUTH provider
   * @return DocuSignProvider $provider
   */
  public function get_oauth_provider(): DocuSignProvider {
    return new DocuSignProvider(
      [
        'clientId' => env('DS_CLIENT_ID', ''),
        'clientSecret' => env('DS_CLIENT_SECRET', ''),
        'redirectUri' => route('docusign-oauth-jwt'),
        'authorizationServer' => env('DS_AUTHORIZATION_SERVER', ''),
        'allowSilentAuth' => env('ALLOW_SILENT_AUTHENTICATION', false),
      ]
    );
  }
  
  public function createSessionVars(): void {
    // We have an access token, which we may use in authenticated
    // requests against the service provider's API.
    $account_info = self::$account[0]->getAccounts();
    $base_uri_suffix = '/restapi';
    session([
      'ds_access_token' => self::$access_token->getAccessToken(),
      // expiration time.
      'ds_expiration' => time() + (self::$access_token->getExpiresIn() * 60),
      // Using the access token, we may look up details about the
      // resource owner.
      'ds_user_name' => self::$account[0]->getName(),
      'ds_user_email' => self::$account[0]->getEmail(),
      'ds_account_id' => $account_info[0]->getAccountId(),
      'ds_account_name' => $account_info[0]->getAccountName(),
      'ds_base_path' => $account_info[0]->getBaseUri() . $base_uri_suffix,
    ]);
  }
}
