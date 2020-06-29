import React from 'react';

export default {
  login: ({error, data}) => {
    if (error) {
      return Promise.reject(error);
    }
    const idToken = data.authResponseWithAccessToken.idToken;
    localStorage.setItem('token', {
      rawIdToken: idToken.rawIdToken,
      // JS works with timestamps in milliseconds
      expiresAt: idToken.claims.exp * 1000,
    });
    return Promise.resolve();
  },
  checkAuth: () => {
    const token = localStorage.getItem('token');
    token && token.expiresAt > Date.now()
      ? Promise.resolve()
      : Promise.reject()
  },
  checkError: (error) => {
    const status = error.status;
    if (status === 401 || status === 403) {
      localStorage.removeItem('token');
      return Promise.reject();
    }
    return Promise.resolve();
  },
  logout: () => {
    localStorage.removeItem('token');
    return Promise.resolve();
  },
};
