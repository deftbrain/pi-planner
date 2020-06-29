import React from 'react';

export default {
  login: ({error, data}) => {
    if (error) {
      return Promise.reject(error);
    }
    const idToken = data.authResponseWithAccessToken.idToken;
    localStorage.setItem('token', idToken.rawIdToken);
    localStorage.setItem('tokenExpiresAt', idToken.claims.exp * 1000);
    return Promise.resolve();
  },
  checkAuth: () => {
    const token = localStorage.getItem('token');
    const tokenExpiresAt = localStorage.getItem('tokenExpiresAt');
    return token && tokenExpiresAt > Date.now()
      ? Promise.resolve()
      : Promise.reject()
  },
  checkError: (error) => {
    const status = error.status;
    if (status === 401 || status === 403) {
      localStorage.removeItem('token');
      localStorage.removeItem('tokenExpiresAt');
      return Promise.reject();
    }
    return Promise.resolve();
  },
  logout: () => {
    localStorage.removeItem('token');
    localStorage.removeItem('tokenExpiresAt');
    return Promise.resolve();
  },
  getPermissions: () => Promise.resolve()
};
