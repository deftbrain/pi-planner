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
    case 'PROGRAMINCREMENT_SHOW_MERCURE_MESSAGE':
      return action.retrieved;
    default:
      return state;
  }
}

export function eventSource(state = null, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_MERCURE_OPEN':
      return action.eventSource;

    case 'PROGRAMINCREMENT_SHOW_RESET':
      return null;

    default:
      return state;
  }
}

export function teamFilter(state = '', action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_TEAM_FILTER_SET':
      return action.team;

    case 'PROGRAMINCREMENT_SHOW_RESET':
      return '';

    default:
      return state;
  }
}

export function isReviewModeEnabled(state = false, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_SWITCH_REVIEW_MODE':
      return !state;

    default:
      return state;
  }
}

export default combineReducers({ error, loading, retrieved, eventSource, teamFilter, isReviewModeEnabled });
