import {API_ENTRYPOINT} from './config/app';

const STORAGE_KEY = 'userSession';

function startUserSession(data) {
  return window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

export function finishUserSession() {
  return window.localStorage.removeItem(STORAGE_KEY);
}

export function isUserAuthenticated() {
  const userSessionEncoded = window.localStorage.getItem(STORAGE_KEY);
  return userSessionEncoded && JSON.parse(userSessionEncoded).expiresAt > Date.now() / 1000;
}

export function login(idToken) {
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
          return data.username;
        });
    });
}
