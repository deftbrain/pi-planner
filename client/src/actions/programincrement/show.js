import {fetch, normalize} from '../../utils/dataAccess';
import {retrieve as retrieveProjects} from '../project/list';
import {retrieve as retrieveTeams} from '../team/list';
import {retrieve as retrieveSprints} from '../sprint/list';

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
          .then(retrieved => ({...retrieved}))
      )
      .then((retrieved) => {
        retrieved = normalize(retrieved);
        let projects = [];
        let teams = new Set();
        let sprints = [];
        for (let settings of retrieved.projectSettings) {
          projects.push(settings.project);
          for (let capacity of settings.capacity || []) {
            teams.add(capacity.team);
          }
          sprints = sprints.concat(settings.sprints);
        }
        dispatch(retrieveProjects(projects));
        dispatch(retrieveTeams([...teams]));
        dispatch(retrieveSprints(sprints));
        dispatch(loading(false));
        dispatch(success(retrieved));
      })
      .catch(e => {
        dispatch(loading(false));
        dispatch(error(e.message));
      });
  };
}
