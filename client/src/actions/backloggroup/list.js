import {fetch, normalize} from '../../utils/dataAccess';

export function error(error) {
  return {type: 'BACKLOG_GROUP_LIST_ERROR', error};
}

export function loading(loading) {
  return {type: 'BACKLOG_GROUP_LIST_LOADING', loading};
}

export function success(retrieved) {
  return {type: 'BACKLOG_GROUP_LIST_SUCCESS', retrieved};
}

export function retrieve(projectIds) {
  return dispatch => {
    dispatch(loading(true));
    dispatch(error(''));

    fetch('backlog_groups', {}, {projects: projectIds})
      .then(response =>
        response
          .json()
          .then(retrieved => ({retrieved}))
      )
      .then(({retrieved}) => {
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

export function reset() {
  return dispatch => {
    dispatch({type: 'BACKLOG_GROUP_LIST_RESET'});
  };
}
