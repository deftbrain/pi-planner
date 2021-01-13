import {extractHubURL, fetch, mercureSubscribe as subscribe, normalize} from '../../utils/dataAccess';
import {retrieve as retrieveBacklogGroups} from '../backloggroup/list';
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
        const projects = retrieved.projectsSettings.map(ps => ps.project);
        const teams = new Set()
        retrieved.projectsSettings.forEach(ps => ps.teamSprintCapacities.forEach(tc => teams.add(tc.team)));
        const sprints = new Set()
        retrieved.projectsSettings.forEach(c => c.sprints.forEach(s => sprints.add(s)));
        dispatch(retrieveProjects(projects));
        dispatch(retrieveTeams([...teams]));
        dispatch(retrieveSprints([...sprints]));
        dispatch(retrieveEstimates(id));
        dispatch(retrieveBacklogGroups(projects));
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

export function setTeamFilter(team) {
  return dispatch => {
    dispatch({type: 'PROGRAMINCREMENT_TEAM_FILTER_SET', team});
  };
}

export function switchReviewMode() {
  return dispatch => {
    dispatch({type: 'PROGRAMINCREMENT_SHOW_SWITCH_REVIEW_MODE'});
  };
}
