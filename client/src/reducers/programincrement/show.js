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

const TEAM_FILTER_CACHE_KEY = 'selectedTeam';
export function teamFilter(state = localStorage.getItem(TEAM_FILTER_CACHE_KEY) || '', action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_TEAM_FILTER_SET':
      localStorage.setItem(TEAM_FILTER_CACHE_KEY, action.team);
      return action.team;

    case 'PROGRAMINCREMENT_SHOW_RESET':
      localStorage.removeItem(TEAM_FILTER_CACHE_KEY);
      return '';

    default:
      return state;
  }
}

const REVIEW_MODE_CACHE_KEY = 'reviewMode';
export function isReviewModeEnabled(state = localStorage.getItem(REVIEW_MODE_CACHE_KEY) === 'true', action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_SWITCH_REVIEW_MODE':
      const newState = !state;
      localStorage.setItem(REVIEW_MODE_CACHE_KEY, newState);
      return newState;

    default:
      return state;
  }
}

const dependencyManagerInitialState = {workitem: null};

export function dependencyManager(state = dependencyManagerInitialState, action) {
  switch (action.type) {
    case 'PROGRAMINCREMENT_SHOW_ENABLE_DEPENDENCY_MANAGER':
      return {workitem: action.workitem};

    case 'PROGRAMINCREMENT_SHOW_DISABLE_DEPENDENCY_MANAGER':
      return dependencyManagerInitialState;

    default:
      return state;
  }
}

export default combineReducers({
  error,
  loading,
  retrieved,
  eventSource,
  teamFilter,
  isReviewModeEnabled,
  dependencyManager
});
