import React from 'react';
import {Redirect, Route} from 'react-router-dom';
import {
  fetchHydra as baseFetchHydra,
  HydraAdmin,
  hydraDataProvider as baseHydraDataProvider,
} from '@api-platform/admin';
import {Resource} from 'react-admin';
import parseHydraDocumentation from "@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation";
import programIncrementComponents from './resource/programIncrement';
import epicComponents from './resource/epic';
import {TeamSprintCapacityCreate, TeamSprintCapacityEdit} from './resource/teamSprintCapacity';
import LoginPage from './Login';
import authProvider, {isUserAuthenticated} from './authProvider';
import {API_ENTRYPOINT} from './config/app';

const authOptions = {credentials: 'include'};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {...options, ...authOptions});
const apiDocumentationParser = entrypoint => {
  return parseHydraDocumentation(entrypoint, authOptions)
    .then(
      ({api}) => ({api}),
      (result) => {
        switch (result.status) {
          case 401:
            return Promise.resolve({
              api: result.api,
              customRoutes: [
                <Route path="/" render={() => {
                  return isUserAuthenticated() ? window.location.reload() : <Redirect to="/login"/>;
                }}/>
              ],
            });

          default:
            return Promise.reject(result);
        }
      },
    );
};

const dataProvider = baseHydraDataProvider(API_ENTRYPOINT, fetchHydra, apiDocumentationParser);

export default () => (
  <HydraAdmin entrypoint={API_ENTRYPOINT} dataProvider={dataProvider} authProvider={authProvider}
              loginPage={LoginPage}>
    <Resource name="program_increments" {...programIncrementComponents}/>
    <Resource name="team_sprint_capacities" create={TeamSprintCapacityCreate} edit={TeamSprintCapacityEdit}/>
    <Resource name="backlog_groups"/>
    <Resource name="epics" {...epicComponents}/>
    <Resource name="epic_statuses"/>
    <Resource name="projects"/>
    <Resource name="sprints"/>
    <Resource name="sprint_schedules"/>
    <Resource name="teams"/>
    <Resource name="workitems"/>
  </HydraAdmin>
);
