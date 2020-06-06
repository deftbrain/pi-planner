import {fetch, normalize} from '../../utils/dataAccess';

export function error(error) {
  return { type: 'PROGRAMINCREMENT_SHOW_ERROR', error };
}

export function loading(loading) {
  return { type: 'PROGRAMINCREMENT_SHOW_LOADING', loading };
}

export function success(retrieved) {
  return { type: 'PROGRAMINCREMENT_SHOW_SUCCESS', retrieved };
}

export function retrieve(id) {
  return dispatch => {
    dispatch(loading(true));

    return fetch(id)
      .then(response =>
        response
          .json()
          .then(retrieved => ({...retrieved}))
      )
      .then((retrieved) => {
        retrieved = normalize(retrieved);

        dispatch(loading(false));
        dispatch(success(retrieved));
      })
      .catch(e => {
        dispatch(loading(false));
        dispatch(error(e.message));
      });
  };
}
