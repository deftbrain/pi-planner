import { combineReducers } from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'TEAM_SHOW_ERROR':
      return action.error;

    case 'TEAM_SHOW_MERCURE_DELETED':
      return `${action.retrieved['@id']} has been deleted by another user.`;

    case 'TEAM_SHOW_RESET':
      return null;

    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'TEAM_SHOW_LOADING':
      return action.loading;

    case 'TEAM_SHOW_RESET':
      return false;

    default:
      return state;
  }
}

export function retrieved(state = null, action) {
  switch (action.type) {
    case 'TEAM_SHOW_SUCCESS':
    case 'TEAM_SHOW_MERCURE_MESSAGE':
      return action.retrieved;

    case 'TEAM_SHOW_RESET':
      return null;

    default:
      return state;
  }
}

export function eventSource(state = null, action) {
  switch (action.type) {
    case 'TEAM_SHOW_MERCURE_OPEN':
      return action.eventSource;

    case 'TEAM_SHOW_RESET':
      return null;

    default:
      return state;
  }
}

export default combineReducers({ error, loading, retrieved, eventSource });
