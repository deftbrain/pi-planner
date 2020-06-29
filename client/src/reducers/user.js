import {combineReducers} from 'redux';

export function account(state = null, action) {
  switch (action.type) {
    case 'USER_AUTHENTICATED':
      return action.account;

    case 'USER_AUTHENTICATION_FAILED':
      return null;

    default:
      return state;
  }
}

export function error(state = null, action) {
  switch (action.type) {
    case 'USER_AUTHENTICATED':
      return null;

    case 'USER_AUTHENTICATION_FAILED':
      return action.error;

    default:
      return state;
  }
}

export default combineReducers({account, error});
