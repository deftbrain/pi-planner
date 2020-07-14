import React from 'react';

const TOKEN_KEY = 'token';
const TOKEN_EXPIRES_AT_KEY = 'tokenExpiresAt';
export const AUTHENTICATION_SCHEME = 'Bearer';

export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

export function isTokenValid() {
  return getToken() && localStorage.getItem(TOKEN_EXPIRES_AT_KEY) > Date.now();
}

export default {
  login: ({error, data}) => {
    if (error) {
      return Promise.reject(error);
    }
    const idToken = data.authResponseWithAccessToken.idToken;
    localStorage.setItem(TOKEN_KEY, idToken.rawIdToken);
    localStorage.setItem(TOKEN_EXPIRES_AT_KEY, idToken.claims.exp * 1000);
    return Promise.resolve();
  },
  checkAuth: () => {
    return isTokenValid() ? Promise.resolve() : Promise.reject()
  },
  checkError: (error) => {
    const status = error.status;
    if (status === 401 || status === 403) {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(TOKEN_EXPIRES_AT_KEY);
      return Promise.reject();
    }
    return Promise.resolve();
  },
  logout: () => {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(TOKEN_EXPIRES_AT_KEY);
    return Promise.resolve();
  },
  getPermissions: () => Promise.resolve()
};
