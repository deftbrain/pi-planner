import {combineReducers} from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_ERROR':
      return action.error;
    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_LOADING':
      return action.loading;
    default:
      return state;
  }
}

export function retrieved(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_SUCCESS':
      return action.retrieved;
    default:
      return state;
  }
}

export default combineReducers({error, loading, retrieved});
