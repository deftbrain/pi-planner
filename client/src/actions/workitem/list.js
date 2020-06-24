import {extractHubURL, fetch, mercureSubscribe as subscribe, normalize} from '../../utils/dataAccess';
import {success as deleteSuccess} from './delete';

export function error(error) {
  return {type: 'WORKITEM_LIST_ERROR', error};
}

export function loading(loading) {
  return {type: 'WORKITEM_LIST_LOADING', loading};
}

export function success(retrieved, epic) {
  return { type: 'WORKITEM_LIST_SUCCESS', retrieved, epic };
}

export function list(epic) {
  return dispatch => {
    dispatch(loading(true));
    dispatch(error(''));

    fetch('workitems', {}, {epic: epic, isDeleted: false})
      .then(response =>
        response
          .json()
          .then(retrieved => ({ retrieved, hubURL: extractHubURL(response) }))
      )
      .then(({ retrieved, hubURL }) => {
        retrieved = normalize(retrieved);

        dispatch(loading(false));
        dispatch(success(retrieved, epic));

        if (hubURL && retrieved['@id'])
          dispatch(
            mercureSubscribe(
              hubURL,
              [retrieved['@id'] + '/{id}']
            )
          );
      })
      .catch(e => {
        dispatch(loading(false));
        dispatch(error(e.message));
      });
  };
}

export function reset(eventSource) {
  return dispatch => {
    if (eventSource) eventSource.close();

    dispatch({ type: 'WORKITEM_LIST_RESET' });
    dispatch(deleteSuccess(null));
  };
}

export function mercureSubscribe(hubURL, topics) {
  return dispatch => {
    const eventSource = subscribe(hubURL, topics);
    dispatch(mercureOpen(eventSource));
    eventSource.addEventListener('message', event =>
      dispatch(mercureMessage(normalize(JSON.parse(event.data))))
    );
  };
}

export function mercureOpen(eventSource) {
  return { type: 'WORKITEM_LIST_MERCURE_OPEN', eventSource };
}

export function mercureMessage(retrieved) {
  return dispatch => {
    if (retrieved.isDeleted) {
      dispatch({ type: 'WORKITEM_LIST_MERCURE_DELETED', retrieved });
      return;
    }

    dispatch({ type: 'WORKITEM_LIST_MERCURE_MESSAGE', retrieved });
  };
}
