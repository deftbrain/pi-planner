import React from 'react'
import {MICROSOFT_OAUTH_CLIENT_ID, MICROSOFT_OAUTH_TENANT_ENDPOINT} from './config/app';
import MicrosoftLogin from 'react-microsoft-login';
import {useHistory, useLocation} from 'react-router-dom';
import {login} from './authProvider';

export default props => {
  const location = useLocation();
  const {from} = location.state || {from: {pathname: '/'}};
  const history = useHistory();
  let message;

  async function authCallback(error, data) {
    // Clear tokens cached by the MSAL library: https://www.npmjs.com/package/msal#cache-storage
    sessionStorage.clear();
    if (error) {
      message = error.message;
      return;
    }

    // TODO: Figure out why it doesn't work
    message = 'Authenticating...';
    login(data.authResponseWithAccessToken.idToken.rawIdToken)
      .then(username => {
        message = `Authenticated as ${username}. Redirecting to the previous page...`;
        history.replace(from)
      })
      .catch(error => message = error.message);
  }

  return (
    <div>
      <MicrosoftLogin clientId={MICROSOFT_OAUTH_CLIENT_ID} tenantUrl={MICROSOFT_OAUTH_TENANT_ENDPOINT}
                      authCallback={authCallback}/>
      {message && <p>{message}</p>}
    </div>
  );
}
