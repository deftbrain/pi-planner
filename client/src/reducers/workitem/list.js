import { combineReducers } from 'redux';

export function error(state = null, action) {
  switch (action.type) {
    case 'WORKITEM_LIST_ERROR':
      return action.error;

    case 'WORKITEM_LIST_MERCURE_DELETED':
      return `${action.retrieved['@id']} has been deleted by another user.`;

    case 'WORKITEM_LIST_RESET':
      return null;

    default:
      return state;
  }
}

export function loading(state = false, action) {
  switch (action.type) {
    case 'WORKITEM_LIST_LOADING':
      return action.loading;

    case 'WORKITEM_LIST_RESET':
      return false;

    default:
      return state;
  }
}

export function retrieved(state = {}, action) {
  let newState;
  switch (action.type) {
    case 'WORKITEM_LIST_SUCCESS':
      return {...state, [action.epic]: action.retrieved};

    case 'WORKITEM_LIST_RESET':
      newState = Object.assign({}, state);
      delete newState[action.epic];
      return newState;

    case 'WORKITEM_LIST_MERCURE_MESSAGE':
      const retrieved = action.retrieved;
      if (!state[retrieved.epic]) {
        // Ignore changes not related to open epics
        return state;
      }
      newState = Object.assign({}, state);
      const retrievedId = retrieved['@id'];
      const index = state[retrieved.epic]['hydra:member'].findIndex((w) => w['@id'] === retrievedId);
      if (-1 === index) {
        newState[retrieved.epic]['hydra:member'].push(retrieved);
      } else {
        newState[retrieved.epic]['hydra:member'][index] = retrieved;
      }
      return newState;

    case 'WORKITEM_LIST_MERCURE_DELETED':
      const removedId = action.retrieved['@id'];
      for (let epic in state) {
        const index = state[epic]['hydra:member'].findIndex(w => w['@id'] === removedId);
        if (-1 !== index) {
          newState = Object.assign({}, state);
          delete newState[epic]['hydra:member'][index];
          return newState;
        }
      }
      return state;

    default:
      return state;
  }
}

export function eventSource(state = null, action) {
  switch (action.type) {
    case 'WORKITEM_LIST_MERCURE_OPEN':
      return action.eventSource;

    case 'WORKITEM_LIST_RESET':
      return null;

    default:
      return state;
  }
}

export default combineReducers({ error, loading, retrieved, eventSource });
