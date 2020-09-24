import {API_ENTRYPOINT} from './config/app';

const STORAGE_KEY = 'userSession';

export function isUserAuthenticated() {
  const userSessionEncoded = window.localStorage.getItem(STORAGE_KEY);
  return userSessionEncoded && JSON.parse(userSessionEncoded).expiresAt > Date.now() / 1000;
}

function startUserSession(data) {
  return window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

export function finishUserSession() {
  return window.localStorage.removeItem(STORAGE_KEY);
}

export default {
  login: ({data, error}) => {
    if (error) {
      return Promise.reject(error);
    }
    const idToken = data.authResponseWithAccessToken.idToken.rawIdToken;
    const request = new Request(API_ENTRYPOINT + '/login', {
      credentials: 'include',
      headers: new Headers({'Authorization': 'Bearer ' + idToken}),
    });
    return fetch(request)
      .then(response => {
        if (!response.ok) {
          throw new Error(response.statusText);
        }

        return response.json()
          .then(data => {
            startUserSession(data);
          });
      });
  },
  checkAuth: () => isUserAuthenticated() ? Promise.resolve() : Promise.reject(),
  checkError: error => Promise.reject(error),
  logout: () => {
    finishUserSession()
    const request = new Request(API_ENTRYPOINT + '/logout', {credentials: 'include'});
    return fetch(request).then(() => Promise.resolve());
  },
  getPermissions: () => Promise.resolve()
};
