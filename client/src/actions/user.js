export function authenticated(account) {
  return {type: 'USER_AUTHENTICATED', account};
}

export function authenticationFailed(error) {
  return {type: 'USER_AUTHENTICATION_FAILED', error};
}
