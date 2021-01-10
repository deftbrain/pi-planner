import {SubmissionError} from 'redux-form';
import {fetch} from '../../utils/dataAccess';

export function error(error) {
  return {type: 'WORKITEM_CREATE_ERROR', error};
}

export function loading(loading) {
  return {type: 'WORKITEM_CREATE_LOADING', loading};
}

export function success(created) {
  return { type: 'WORKITEM_CREATE_SUCCESS', created };
}

export function create(values) {
  return dispatch => {
    dispatch(error(null))
    dispatch(loading(true));

    return fetch('workitems', {
      method: 'POST',
      body: JSON.stringify(values),
      headers: new Headers({'Content-Type': 'application/json'})
    })
      .then(response => {
        dispatch(loading(false));

        return response.json();
      })
      .then(retrieved => dispatch(success(retrieved)))
      .catch(e => {
        dispatch(loading(false));

        if (e instanceof SubmissionError) {
          dispatch(error(e.errors._error));
        } else {
          dispatch(error(e.message));
        }

        throw e;
      });
  };
}

export function reset() {
  return dispatch => {
    dispatch(loading(false));
    dispatch(error(null));
    dispatch(success(null));
  };
}
