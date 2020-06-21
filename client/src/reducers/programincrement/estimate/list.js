import {combineReducers} from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_ESTIMATE_LIST_ERROR':
      return action.error;
    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_ESTIMATE_LIST_LOADING':
      return action.loading;
    default:
      return state;
  }
}

export function retrieved(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_ESTIMATE_LIST_SUCCESS':
    case 'PROGRAMINCREMENT_ESTIMATE_LIST_MERCURE_MESSAGE':
      return action.retrieved;
    default:
      return state;
  }
}

export function eventSource(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_ESTIMATE_LIST_MERCURE_OPEN':
      return action.eventSource;

    case 'PROGRAMINCREMENT_ESTIMATE_LIST_RESET':
      return null;

    default:
      return state;
  }
}

export default combineReducers({error, loading, retrieved, eventSource});
