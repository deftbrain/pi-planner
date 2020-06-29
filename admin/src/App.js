import React from 'react';
import {Redirect, Route} from 'react-router-dom';
import {
  fetchHydra as baseFetchHydra,
  HydraAdmin,
  hydraDataProvider as baseHydraDataProvider,
  ResourceGuesser
} from '@api-platform/admin';
import parseHydraDocumentation from "@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation";
import {WorkitemList} from './resource/WorkitemList';
import {ProgramIncrementList} from './resource/ProgramIncrementList';
import {ProgramIncrementCreate} from './resource/ProgramIncremenCreate';
import {ProgramIncrementEdit} from './resource/ProgramIncremenEdit';
import authProvider from './authProvider';
import LoginPage from './Login';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;
const token = window.localStorage.getItem('token');
const fetchHeaders = token ? {Authorization: 'Bearer ' + token} : {};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
  ...options,
  headers: new Headers(fetchHeaders),
});
const apiDocumentationParser = entrypoint => parseHydraDocumentation(entrypoint, {headers: new Headers(fetchHeaders)})
  .then(
    ({api}) => ({api}),
    (result) => {
      switch (result.status) {
        case 401:
          return Promise.resolve({
            api: result.api,
            customRoutes: [
              <Route path="/" render={() => {
                return window.localStorage.getItem('token') ? window.location.reload() : <Redirect to="/login"/>
              }}/>
            ],
          });

        default:
          return Promise.reject(result);
      }
    },
  );
const dataProvider = baseHydraDataProvider(entrypoint, fetchHydra, apiDocumentationParser);
export default () => (
  <HydraAdmin entrypoint={entrypoint} dataProvider={dataProvider} authProvider={authProvider}
              loginPage={LoginPage}>
    <ResourceGuesser name="backlog_groups"/>
    <ResourceGuesser name="epics"/>
    <ResourceGuesser name="epic_statuses"/>
    <ResourceGuesser name="program_increments" list={ProgramIncrementList} create={ProgramIncrementCreate}
                     edit={ProgramIncrementEdit}/>
    <ResourceGuesser name="projects"/>
    <ResourceGuesser name="sprints"/>
    <ResourceGuesser name="sprint_schedules"/>
    <ResourceGuesser name="teams"/>
    <ResourceGuesser name="workitems" list={WorkitemList}/>
  </HydraAdmin>
);
