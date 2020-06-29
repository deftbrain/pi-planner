import React from 'react';
import {Notification, useLogin, useNotify} from 'react-admin';
import {ThemeProvider} from '@material-ui/styles';
import MicrosoftLogin from 'react-microsoft-login';

export default ({theme}) => {
  const login = useLogin();
  const notify = useNotify();
  const authCallback = (error, data) => {
    login({error, data}).catch(error => notify(error));
  }
  return (
    <ThemeProvider theme={theme}>
      <MicrosoftLogin clientId={process.env.REACT_APP_MICROSOFT_OAUTH_CLIENT_ID}
                      tenantUrl={process.env.REACT_APP_MICROSOFT_OAUTH_TENANT_ENDPOINT}
                      authCallback={authCallback}/>
      <Notification/>
    </ThemeProvider>
  );
};
