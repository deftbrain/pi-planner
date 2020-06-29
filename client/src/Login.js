import React from 'react'
import {connect} from 'react-redux';
import {MICROSOFT_OAUTH_CLIENT_ID, MICROSOFT_OAUTH_TENANT_ENDPOINT} from './config/app';
import MicrosoftLogin from 'react-microsoft-login';
import {useHistory, useLocation} from 'react-router-dom';
import {authenticated, authenticationFailed} from './actions/user';

const Login = props => {
  const location = useLocation();
  const {from} = location.state || {from: {pathname: '/'}};
  const history = useHistory();

  const authCallback = (error, data) => {
    if (error) {
      props.saveError(error.message);
    } else {
      const idToken = data.authResponseWithAccessToken.idToken;
      props.saveUserData({
        identity: {
          token: idToken.rawIdToken,
          // JS works with timestamps in milliseconds
          expiresAt: idToken.claims.exp * 1000,
        },
      });
      history.replace(from);
    }
  }

  return (
    <div>
      <MicrosoftLogin clientId={MICROSOFT_OAUTH_CLIENT_ID} tenantUrl={MICROSOFT_OAUTH_TENANT_ENDPOINT}
                      authCallback={authCallback}/>
      {props.error && <p>{props.error}</p>}
    </div>
  );
}


const mapStateToProps = (state) => {
  return {error: state.user.error};
};

const mapDispatchToProps = dispatch => ({
  saveUserData: account => dispatch(authenticated(account)),
  saveError: error => dispatch(authenticationFailed(error)),
});

export default connect(mapStateToProps, mapDispatchToProps)(Login);
