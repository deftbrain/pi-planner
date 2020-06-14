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
      newState = Object.assign({}, state);
      newState[action.retrieved.epic]['hydra:member'] = newState[action.retrieved.epic]['hydra:member'].map(item =>
        item['@id'] === action.retrieved['@id'] ? action.retrieved : item
      )
      return newState;

    case 'WORKITEM_LIST_MERCURE_DELETED':
      const removedWorkitemId = action.retrieved['@id'];
      newState = Object.assign({}, state);
      for (let epic in newState) {
        newState[epic]['hydra:member'] = newState[epic]['hydra:member'].filter(
          item => item['@id'] !== removedWorkitemId
        )
      }
      return newState;

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
