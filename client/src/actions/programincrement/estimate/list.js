import {extractHubURL, fetch, mercureSubscribe as subscribe} from '../../../utils/dataAccess';

export function error(error) {
  return {type: 'PROGRAMINCREMENT_ESTIMATE_LIST_ERROR', error};
}

export function loading(loading) {
  return {type: 'PROGRAMINCREMENT_ESTIMATE_LIST_LOADING', loading};
}

export function success(retrieved) {
  return {type: 'PROGRAMINCREMENT_ESTIMATE_LIST_SUCCESS', retrieved};
}

export function mercureSubscribe(hubURL, topic) {
  return dispatch => {
    const eventSource = subscribe(hubURL, [topic]);
    dispatch(mercureOpen(eventSource));
    eventSource.addEventListener('message', event => {
      const data = JSON.parse(event.data);
      dispatch(mercureMessage(data));
      dispatch(loading(false));
    });
  };
}

export function mercureOpen(eventSource) {
  return {type: 'PROGRAMINCREMENT_ESTIMATE_LIST_MERCURE_OPEN', eventSource};
}

export function mercureMessage(retrieved) {
  return dispatch => {
    dispatch({type: 'PROGRAMINCREMENT_ESTIMATE_LIST_MERCURE_MESSAGE', retrieved});
  };
}

export function retrieve(programIncrement) {
  return dispatch => {
    dispatch(loading(true));
    dispatch(error(''));

    const uri = `${programIncrement}/estimates`;
    fetch(uri)
      .then(response =>
        response
          .json()
          .then(retrieved => ({retrieved, hubURL: extractHubURL(response)}))
      )
      .then(({retrieved, hubURL}) => {
        dispatch(loading(false));
        dispatch(success(retrieved['hydra:member']));
        if (hubURL) dispatch(mercureSubscribe(hubURL, uri));
      })
      .catch(e => {
        dispatch(loading(false));
        dispatch(error(e.message));
      });
  };
}
