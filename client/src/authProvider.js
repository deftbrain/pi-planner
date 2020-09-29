import {API_ENTRYPOINT} from './config/app';

const STORAGE_KEY = 'userSession';

function startUserSession(data) {
  return window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

export function getUserData() {
  const encodedData = window.localStorage.getItem(STORAGE_KEY);
  return encodedData && JSON.parse(encodedData);
}

export function finishUserSession() {
  return window.localStorage.removeItem(STORAGE_KEY);
}

export function isUserAuthenticated() {
  const userData = getUserData();
  return userData && userData.expiresAt > Date.now() / 1000;
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
