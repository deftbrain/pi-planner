import {combineReducers} from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'BACKLOG_GROUP_LIST_ERROR':
      return action.error;

    case 'BACKLOG_GROUP_LIST_RESET':
      return null;

    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'BACKLOG_GROUP_LIST_LOADING':
      return action.loading;

    case 'BACKLOG_GROUP_LIST_RESET':
      return false;

    default:
      return state;
  }
}

export function retrieved(state = null, action) {
  switch (action.type) {
    case 'BACKLOG_GROUP_LIST_SUCCESS':
      return action.retrieved;

    case 'BACKLOG_GROUP_LIST_RESET':
      return null;

    default:
      return state;
  }
}

export default combineReducers({error, loading, retrieved});
