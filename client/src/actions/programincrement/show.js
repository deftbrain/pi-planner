import {extractHubURL, fetch, mercureSubscribe as subscribe, normalize} from '../../utils/dataAccess';
import {retrieve as retrieveProjects} from '../project/list';
import {retrieve as retrieveTeams} from '../team/list';
import {retrieve as retrieveSprints} from '../sprint/list';
import {retrieve as retrieveEstimates} from './estimate/list';

export function error(error) {
  return {type: 'PROGRAMINCREMENT_SHOW_ERROR', error};
}

export function loading(loading) {
  return {type: 'PROGRAMINCREMENT_SHOW_LOADING', loading};
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
          .then(retrieved => ({retrieved, hubURL: extractHubURL(response)}))
      )
      .then(({retrieved, hubURL}) => {
        let projects = [retrieved.project];
        let teams = new Set();
        for (let capacity of retrieved.teamSprintCapacities) {
          teams.add(capacity.team);
        }
        let sprints = retrieved.sprints;
        dispatch(retrieveProjects(projects));
        dispatch(retrieveTeams([...teams]));
        dispatch(retrieveSprints(sprints));
        dispatch(retrieveEstimates(id));
        dispatch(loading(false));
        dispatch(success(retrieved));

        if (hubURL) dispatch(mercureSubscribe(hubURL, retrieved['@id']));
      })
      .catch(e => {
        dispatch(loading(false));
        dispatch(error(e.message));
      });
  };
}

export function mercureSubscribe(hubURL, topic) {
  return dispatch => {
    const eventSource = subscribe(hubURL, [topic]);
    dispatch(mercureOpen(eventSource));
    eventSource.addEventListener('message', event =>
      dispatch(mercureMessage(normalize(JSON.parse(event.data))))
    );
  };
}

export function mercureOpen(eventSource) {
  return {type: 'PROGRAMINCREMENT_SHOW_MERCURE_OPEN', eventSource};
}

export function mercureMessage(retrieved) {
  return dispatch => {
    dispatch({type: 'PROGRAMINCREMENT_SHOW_MERCURE_MESSAGE', retrieved});
  };
}
