<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Auth;
use Hash;
use Str;
use App\Models\User;
class MicrosoftController extends Controller
{
  public function signin()
  {
    // Initialize the OAuth client
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => config('microsoft.oauth_app_id'),
      'clientSecret'            => config('microsoft.oauth_app_password'),
      'redirectUri'             => config('microsoft.oauth_redirect_url'),
      'urlAuthorize'            => config('microsoft.oauth_authority').config('microsoft.oauth_authorize_endpoint'),
      'urlAccessToken'          => config('microsoft.oauth_authority').config('microsoft.oauth_token_endpoin'),
      'urlResourceOwnerDetails' => '',
      'scopes'                  => config('microsoft.oauth_scopes')
    ]);
    $authUrl = $oauthClient->getAuthorizationUrl();
    session(['oauthState' => $oauthClient->getState()]);
    return redirect()->away($authUrl);
  }
  public function callback(Request $request)
  {
    $expectedState = session('oauthState');
    $request->session()->forget('oauthState');
    $providedState = $request->query('state');
    if (!isset($expectedState)) {
      return redirect('/');
    }
    if (!isset($providedState) || $expectedState != $providedState) {
      return redirect('/')
        ->with('error', 'Invalid auth state')
        ->with('errorDetail', 'The provided auth state did not match the expected value');
    }
    $authCode = $request->query('code');
    if (isset($authCode)) {
      $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => config('microsoft.oauth_app_id'),
        'clientSecret'            => config('microsoft.oauth_app_password'),
        'redirectUri'             => config('microsoft.oauth_redirect_url'),
        'urlAuthorize'            => config('microsoft.oauth_authority').config('microsoft.oauth_authorize_endpoint'),
        'urlAccessToken'          => config('microsoft.oauth_authority').config('microsoft.oauth_token_endpoin'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => config('microsoft.oauth_scopes')
      ]);
      try {
        $accessToken = $oauthClient->getAccessToken('authorization_code', [
          'code' => $authCode
        ]);
        $graph = new Graph();
        $graph->setAccessToken($accessToken->getToken());
        $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
          ->setReturnType(Model\User::class)
          ->execute();
        $user_firts = User::where('email', $user->getuserPrincipalName())->first();
        if (!empty($user_firts)) { 
          return $this->authAndRedirect($user_firts); // Login y redirecci贸n
        }else{
          $user = User::create([
            // 'token' => $user->token;
            'name' => $user->getgivenName(),
            'email' => $user->getuserPrincipalName(),
            'password' => Hash::make(Str::random(24)),
          ]);
          return $this->authAndRedirect($user); // Login y redirecci贸n
        }
      }
      catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        return redirect('/')
          ->with('error', 'Error requesting access token')
          ->with('errorDetail', $e->getMessage());
      }
    }
    return redirect('/')
      ->with('error', $request->query('error'))
      ->with('errorDetail', $request->query('error_description'));
  }
  public function authAndRedirect($user) {
    Auth::login($user);
    return redirect('/home');
  }
}